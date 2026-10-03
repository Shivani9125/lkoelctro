import { useEffect, useState, useRef } from 'react';
import api from '../services/api';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const LUCKNOW_AREAS = [
  { id: 'gomti_nagar', name: 'Gomti Nagar (East)', lat: 26.8530, lng: 80.9980 },
  { id: 'indira_nagar', name: 'Indira Nagar (North-East)', lat: 26.8780, lng: 80.9850 },
  { id: 'hazratganj', name: 'Hazratganj (Central)', lat: 26.8467, lng: 80.9462 },
  { id: 'aliganj', name: 'Aliganj (North)', lat: 26.8920, lng: 80.9380 },
  { id: 'alambagh', name: 'Alambagh (South)', lat: 26.8150, lng: 80.9100 },
  { id: 'rajajipuram', name: 'Rajajipuram (West)', lat: 26.8520, lng: 80.8850 },
  { id: 'chowk', name: 'Chowk (Old Lucknow)', lat: 26.8680, lng: 80.9020 },
  { id: 'mahanagar', name: 'Mahanagar', lat: 26.8720, lng: 80.9520 },
  { id: 'ashiyana', name: 'Ashiyana', lat: 26.7910, lng: 80.9120 },
  { id: 'charbagh', name: 'Charbagh', lat: 26.8310, lng: 80.9230 },
  { id: 'vikas_nagar', name: 'Vikas Nagar', lat: 26.8980, lng: 80.9580 },
  { id: 'jankipuram', name: 'Jankipuram', lat: 26.9240, lng: 80.9490 },
];

function calculateDistanceKm(lat1, lon1, lat2, lon2) {
  const R = 6371;
  const dLat = (lat2 - lat1) * (Math.PI / 180);
  const dLon = (lon2 - lon1) * (Math.PI / 180);
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
    Math.sin(dLon / 2) * Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c;
}

function findClosestLucknowArea(lat, lng) {
  let closest = LUCKNOW_AREAS[0];
  let minDistance = 999999;
  for (const area of LUCKNOW_AREAS) {
    const dist = calculateDistanceKm(lat, lng, area.lat, area.lng);
    if (dist < minDistance) {
      minDistance = dist;
      closest = area;
    }
  }
  return closest;
}

function Home() {
  const [customerLocation, setCustomerLocation] = useState({
    latitude: 26.8530,
    longitude: 80.9980,
    areaName: 'Gomti Nagar, Lucknow'
  });
  const [locationStatus, setLocationStatus] = useState('🏠 You live in Gomti Nagar, Lucknow');
  const [selectedArea, setSelectedArea] = useState('gomti_nagar');
  const [electricians, setElectricians] = useState([]);
  const [loading, setLoading] = useState(false);
  const [selectedElectrician, setSelectedElectrician] = useState(null);

  const mapContainerRef = useRef(null);
  const mapInstanceRef = useRef(null);
  const markersGroupRef = useRef(null);
  const customerMarkerRef = useRef(null);

  // 1. On mount: automatically fetch coordinates directly from browser geolocation
  useEffect(() => {
    detectLocationFromBrowser();
  }, []);

  // Tell area name from latitude and longitude via Intelligent Geocoding Layer
  const detectAreaFromCoords = async (lat, lng) => {
    setLocationStatus(`Detecting area for (${lat.toFixed(4)}, ${lng.toFixed(4)})...`);
    
    let areaName = 'Lucknow';
    let areaKey = 'gomti_nagar';

    try {
      const res = await api.get(`/geocode?latitude=${lat}&longitude=${lng}`);
      if (res.data && res.data.success) {
        areaName = res.data.formatted || (res.data.area + ', Lucknow');
        areaKey = res.data.area_key || 'gomti_nagar';
      }
    } catch (err) {
      console.warn('Backend geocode API error, using local fallback:', err);
      const closest = findClosestLucknowArea(lat, lng);
      areaName = `${closest.name}, Lucknow`;
      areaKey = closest.id;
    }

    setCustomerLocation({
      latitude: lat,
      longitude: lng,
      areaName: areaName
    });
    setLocationStatus(`🏠 You live in ${areaName}`);
    setSelectedArea(areaKey);
    try {
      localStorage.setItem('electrofix_user_home', JSON.stringify({
        latitude: lat,
        longitude: lng,
        areaName: areaName,
        areaId: areaKey
      }));
    } catch (e) {}
    fetchNearbyElectricians(lat, lng);
  };

  // Fetch location directly from Browser Geolocation
  const detectLocationFromBrowser = () => {
    setLocationStatus('Detecting your location...');
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          const lat = pos.coords.latitude;
          const lng = pos.coords.longitude;
          console.log('Received browser location:', lat, lng);
          detectAreaFromCoords(lat, lng);
        },
        (err) => {
          console.warn('Browser GPS permission denied or error:', err);
          setLocationStatus('Browser location permission denied. Pick an area below.');
          fetchNearbyElectricians(customerLocation.latitude, customerLocation.longitude);
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
      );
    } else {
      setLocationStatus('Browser geolocation not supported.');
    }
  };

  // 2. Fetch nearby active electricians from Laravel API
  const fetchNearbyElectricians = (lat, lng) => {
    setLoading(true);
    api.get(`/electricians/nearby?latitude=${lat}&longitude=${lng}`)
      .then((response) => {
        if (response.data && response.data.success) {
          setElectricians(response.data.data);
        }
      })
      .catch((error) => {
        console.error('Failed to fetch nearby electricians:', error);
      })
      .finally(() => {
        setLoading(false);
      });
  };

  // 3. User manually selects an area in Lucknow
  const handleAreaChange = (areaId) => {
    setSelectedArea(areaId);
    const area = LUCKNOW_AREAS.find((a) => a.id === areaId);
    if (!area) return;

    const areaName = `${area.name}, Lucknow`;
    setCustomerLocation({
      latitude: area.lat,
      longitude: area.lng,
      areaName: areaName
    });
    setLocationStatus(`🏠 You live in ${areaName}`);
    try {
      localStorage.setItem('electrofix_user_home', JSON.stringify({
        latitude: area.lat,
        longitude: area.lng,
        areaName: areaName,
        areaId: areaId
      }));
    } catch (e) {}
    fetchNearbyElectricians(area.lat, area.lng);
  };

  // 4. Initialize & update Leaflet Map
  useEffect(() => {
    if (!customerLocation || !mapContainerRef.current) return;

    // Initialize map if needed
    if (!mapInstanceRef.current) {
      const map = L.map(mapContainerRef.current).setView(
        [customerLocation.latitude, customerLocation.longitude],
        13
      );

      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
      }).addTo(map);

      // Allow clicking on map to set your location pin anywhere and tell the area!
      map.on('click', (e) => {
        const { lat, lng } = e.latlng;
        detectAreaFromCoords(lat, lng);
      });

      markersGroupRef.current = L.layerGroup().addTo(map);
      mapInstanceRef.current = map;
    }

    const map = mapInstanceRef.current;
    const markersGroup = markersGroupRef.current;

    markersGroup.clearLayers();

    // Draggable Customer Marker ("🏠 You Live Here")
    const customerIcon = L.divIcon({
      className: 'custom-customer-icon',
      html: `
        <div style="
          position: relative;
          display: flex;
          flex-direction: column;
          align-items: center;
          cursor: grab;
          user-select: none;
        ">
          <div style="
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            color: #ffffff;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
            box-shadow: 0 4px 14px rgba(37,99,235,0.45);
            border: 2px solid #ffffff;
            display: flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.3px;
          ">
            <span style="font-size: 14px;">🏠</span>
            <span>You Live Here</span>
          </div>
          <div style="
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 8px solid #2563eb;
            margin-top: -1px;
          "></div>
        </div>
      `,
      iconSize: [130, 48],
      iconAnchor: [65, 48],
      popupAnchor: [0, -48]
    });

    const customerMarker = L.marker(
      [customerLocation.latitude, customerLocation.longitude],
      { icon: customerIcon, draggable: true, zIndexOffset: 1000 }
    ).addTo(markersGroup);

    customerMarker.bindPopup(`
      <div style="font-family: inherit; font-size: 13px; text-align: center; padding: 4px; min-width: 190px;">
        <div style="font-size: 20px; margin-bottom: 2px;">🏠</div>
        <strong style="color: #2563eb; font-size: 14px;">You Live Here</strong><br>
        <span style="color: #0f172a; font-weight: 700;">${customerLocation.areaName}</span><br>
        <div style="margin-top: 8px; font-size: 11px; color: #64748b; background: #f8fafc; padding: 6px 10px; border-radius: 6px; border: 1px dashed #cbd5e1; line-height: 1.4;">
          ⚡ All electricians below are calculated based on where you live.<br>
          <em>(Drag this pin to any street or click map to move)</em>
        </div>
      </div>
    `);

    // Handle Marker Drag End - tell area based on dropped coordinates
    customerMarker.on('dragend', (e) => {
      const position = e.target.getLatLng();
      detectAreaFromCoords(position.lat, position.lng);
    });

    customerMarkerRef.current = customerMarker;

    // Electrician Markers (Amber ⚡)
    electricians.forEach((elec) => {
      const elecIcon = L.divIcon({
        className: 'custom-elec-icon',
        html: `
          <div style="
            width: 32px;
            height: 32px;
            background-color: #f59e0b;
            color: #ffffff;
            border-radius: 50%;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 8px rgba(245,158,11,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
          ">⚡</div>
        `,
        iconSize: [32, 32],
        iconAnchor: [16, 16],
      });

      const marker = L.marker([elec.latitude, elec.longitude], { icon: elecIcon })
        .addTo(markersGroup);

      marker.bindPopup(`
        <div style="font-family: inherit; font-size: 13px; min-width: 170px;">
          <strong style="font-size: 14px; color: #0f172a;">⚡ ${elec.name}</strong><br>
          <span style="font-size: 12px; color: #d97706; font-weight: 700;">📍 ${elec.area}</span><br>
          <span style="color: #64748b; font-size: 11px;">${elec.address}</span><br>
          <strong style="color: #2563eb;">Distance: ${elec.distance}</strong><br>
          <span style="font-size: 12px; color: #10b981; font-weight: 600;">📞 ${elec.phone}</span>
        </div>
      `);

      marker.on('click', () => {
        setSelectedElectrician(elec);
      });
    });

    // Center map on customer coordinates
    map.setView([customerLocation.latitude, customerLocation.longitude], 13);
  }, [customerLocation, electricians]);

  // Click card to pan to electrician
  const handleSelectElectrician = (elec) => {
    setSelectedElectrician(elec);
    if (mapInstanceRef.current && elec.latitude && elec.longitude) {
      mapInstanceRef.current.flyTo([elec.latitude, elec.longitude], 15, { duration: 0.8 });
    }
  };

  return (
    <div style={{ maxWidth: '1200px', margin: '0 auto', padding: '24px 20px', fontFamily: 'system-ui, -apple-system, sans-serif' }}>
      
      {/* Header */}
      <header style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '18px', flexWrap: 'wrap', gap: '14px' }}>
        <div>
          <h1 style={{ fontSize: '26px', fontWeight: '800', color: '#0f172a', margin: 0, display: 'flex', alignItems: 'center', gap: '8px' }}>
            <span style={{ background: '#f59e0b', color: 'white', borderRadius: '8px', padding: '4px 10px', fontSize: '18px' }}>⚡</span>
            ElectroFix — Electricians Near Where You Live
          </h1>
          <p style={{ margin: '4px 0 0', color: '#64748b', fontSize: '14px' }}>
            Showing verified electricians sorted closest to your home (<strong>{customerLocation.areaName}</strong>). Drag the <strong>"🏠 You Live Here"</strong> pin to your exact building!
          </p>
        </div>

        <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
          <button
            onClick={detectLocationFromBrowser}
            style={{
              background: '#2563eb',
              color: '#ffffff',
              border: 'none',
              borderRadius: '8px',
              padding: '9px 15px',
              fontSize: '13px',
              fontWeight: '700',
              cursor: 'pointer',
              display: 'flex',
              alignItems: 'center',
              gap: '6px',
              boxShadow: '0 2px 8px rgba(37,99,235,0.3)',
            }}
            title="Detect your location"
          >
            <span>📍</span> Detect My Location
          </button>

          <div style={{ background: '#eff6ff', border: '1px solid #bfdbfe', borderRadius: '8px', padding: '8px 14px', fontSize: '13px', fontWeight: '700', color: '#1e40af' }}>
            🏠 {customerLocation.areaName}
          </div>
        </div>
      </header>

      {/* Area Switcher Tabs */}
      <div style={{ display: 'flex', alignItems: 'center', gap: '8px', overflowX: 'auto', paddingBottom: '10px', marginBottom: '16px' }}>
        <span style={{ fontSize: '13px', fontWeight: '700', color: '#64748b', marginRight: '4px', whiteSpace: 'nowrap' }}>
          Quick Home Area:
        </span>
        {LUCKNOW_AREAS.map((area) => {
          const isActive = selectedArea === area.id;
          return (
            <button
              key={area.id}
              onClick={() => handleAreaChange(area.id)}
              style={{
                background: isActive ? '#0f172a' : '#ffffff',
                color: isActive ? '#ffffff' : '#475569',
                border: `1px solid ${isActive ? '#0f172a' : '#cbd5e1'}`,
                borderRadius: '9999px',
                padding: '6px 14px',
                fontSize: '13px',
                fontWeight: '600',
                cursor: 'pointer',
                whiteSpace: 'nowrap',
                transition: 'all 0.2s ease',
                boxShadow: isActive ? '0 2px 8px rgba(15,23,42,0.2)' : 'none'
              }}
            >
              {area.name}
            </button>
          );
        })}
      </div>

      {/* Main Grid: Map (Left) + Electricians Cards (Right) */}
      <div style={{ display: 'grid', gridTemplateColumns: '1.25fr 1fr', gap: '24px' }}>
        
        {/* Map Container */}
        <div>
          <div
            ref={mapContainerRef}
            style={{
              width: '100%',
              height: '560px',
              borderRadius: '12px',
              border: '1px solid #e2e8f0',
              boxShadow: '0 4px 12px rgba(0,0,0,0.06)',
              overflow: 'hidden',
            }}
          />
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '10px', fontSize: '12px', color: '#64748b', fontWeight: '600' }}>
            <div style={{ display: 'flex', gap: '16px' }}>
              <span style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                <span style={{ fontSize: '14px' }}>🏠</span>
                <strong style={{ color: '#2563eb' }}>You Live Here (Draggable)</strong>
              </span>
              <span style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                <span style={{ width: '12px', height: '12px', borderRadius: '50%', background: '#f59e0b', display: 'inline-block' }}></span>
                ⚡ Active Electrician
              </span>
            </div>
            <span style={{ color: '#475569', fontStyle: 'italic' }}>
              💡 Tip: Click map or drag pin to set where you live!
            </span>
          </div>
        </div>

        {/* Electricians Cards Column */}
        <div style={{ display: 'flex', flexDirection: 'column', gap: '12px', maxHeight: '580px', overflowY: 'auto', paddingRight: '4px' }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
            <h3 style={{ fontSize: '17px', fontWeight: '700', color: '#0f172a', margin: 0 }}>
              Electricians Nearest to Your Home ({electricians.length})
            </h3>
            {loading && <span style={{ fontSize: '12px', color: '#2563eb', fontWeight: '600' }}>Calculating distances...</span>}
          </div>

          {electricians.length === 0 ? (
            <div style={{
              background: '#ffffff',
              border: '1px dashed #cbd5e1',
              borderRadius: '12px',
              padding: '36px 20px',
              textAlign: 'center',
              color: '#64748b'
            }}>
              <div style={{ fontSize: '32px', marginBottom: '8px' }}>⚡</div>
              <h4 style={{ fontSize: '16px', fontWeight: '700', color: '#0f172a', marginBottom: '6px' }}>
                No active electricians found
              </h4>
            </div>
          ) : (
            electricians.map((elec) => {
              const isSelected = selectedElectrician?.id === elec.id;
              return (
                <div
                  key={elec.id}
                  onClick={() => handleSelectElectrician(elec)}
                  style={{
                    background: '#ffffff',
                    border: `1px solid ${isSelected ? '#2563eb' : '#e2e8f0'}`,
                    borderRadius: '10px',
                    padding: '14px 16px',
                    cursor: 'pointer',
                    boxShadow: isSelected ? '0 4px 14px rgba(37,99,235,0.15)' : '0 1px 3px rgba(0,0,0,0.04)',
                    transition: 'all 0.2s ease',
                    backgroundColor: isSelected ? '#f8fafc' : '#ffffff',
                  }}
                >
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '6px' }}>
                    <div>
                      <h4 style={{ fontSize: '15px', fontWeight: '700', color: '#0f172a', margin: '0 0 4px' }}>
                        {elec.name}
                      </h4>
                      <p style={{ fontSize: '12px', color: '#d97706', fontWeight: '700', margin: '0 0 2px' }}>
                        📍 {elec.area}
                      </p>
                      <p style={{ fontSize: '12px', color: '#64748b', margin: 0, lineHeight: '1.4' }}>
                        {elec.address}
                      </p>
                    </div>
                    <span style={{
                      background: '#eff6ff',
                      color: '#2563eb',
                      fontWeight: '700',
                      fontSize: '12px',
                      padding: '4px 10px',
                      borderRadius: '9999px',
                      whiteSpace: 'nowrap',
                    }}>
                      {elec.distance} from home
                    </span>
                  </div>

                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', fontSize: '12px', marginTop: '10px', paddingTop: '8px', borderTop: '1px dashed #f1f5f9' }}>
                    <span style={{ color: '#10b981', fontWeight: '600' }}>
                      ● {elec.status}
                    </span>
                    <span style={{ color: '#0f172a', fontWeight: '600' }}>
                      📞 {elec.phone}
                    </span>
                  </div>
                </div>
              );
            })
          )}
        </div>
      </div>
    </div>
  );
}

export default Home;
