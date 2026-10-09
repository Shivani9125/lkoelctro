import { useEffect, useState, useRef } from 'react';
import api from '../services/api';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import ElectroFixRobot from '../components/ElectroFixRobot';

const LUCKNOW_AREAS = [
  { id: 'gomti_nagar', name: 'Gomti Nagar (East)', lat: 26.8530, lng: 80.9980, eta: '18 min' },
  { id: 'indira_nagar', name: 'Indira Nagar (North-East)', lat: 26.8780, lng: 80.9850, eta: '20 min' },
  { id: 'hazratganj', name: 'Hazratganj (Central)', lat: 26.8467, lng: 80.9462, eta: '22 min' },
  { id: 'aliganj', name: 'Aliganj (North)', lat: 26.8920, lng: 80.9380, eta: '24 min' },
  { id: 'alambagh', name: 'Alambagh (South)', lat: 26.8150, lng: 80.9100, eta: '25 min' },
  { id: 'rajajipuram', name: 'Rajajipuram (West)', lat: 26.8520, lng: 80.8850, eta: '25 min' },
  { id: 'chowk', name: 'Chowk (Old Lucknow)', lat: 26.8680, lng: 80.9020, eta: '22 min' },
  { id: 'mahanagar', name: 'Mahanagar', lat: 26.8720, lng: 80.9520, eta: '20 min' },
  { id: 'ashiyana', name: 'Ashiyana', lat: 26.7910, lng: 80.9120, eta: '26 min' },
  { id: 'charbagh', name: 'Charbagh', lat: 26.8310, lng: 80.9230, eta: '22 min' },
  { id: 'vikas_nagar', name: 'Vikas Nagar', lat: 26.8980, lng: 80.9580, eta: '23 min' },
  { id: 'jankipuram', name: 'Jankipuram', lat: 26.9240, lng: 80.9490, eta: '28 min' },
];

const SERVICES_CATALOG = [
  {
    id: 'fan',
    category: 'fans',
    title: 'Ceiling & Exhaust Fan Care',
    icon: '🌀',
    price: '₹149',
    time: '30-45 mins',
    desc: 'Humming sound, capacitor change, motor bearing greasing, wobble fix, or new fan installation.',
    features: ['2.5 µF Capacitor testing', 'Ball-bearing lubrication', 'Blade pitch balance check', '30-Day Guarantee']
  },
  {
    id: 'switchboard',
    category: 'switches',
    title: 'Switch, Socket & Board Fix',
    icon: '🔌',
    price: '₹99',
    time: '20-30 mins',
    desc: 'Burnt modular switch, sparking socket, burnt fan regulator, or multi-plug heavy power board replacement.',
    features: ['16A / 6A modular switches', 'Fire-retardant terminal screws', 'Phase & Neutral load test', '30-Day Guarantee']
  },
  {
    id: 'mcb',
    category: 'breakers',
    title: 'MCB Tripping & Short Circuit',
    icon: '⚡',
    price: '₹199',
    time: '30-60 mins',
    desc: 'Frequent breaker tripping with AC or geyser, neutral wire fault, burnt breaker, or line voltage fluctuation.',
    features: ['Digital multimeter circuit test', 'Overload phase balancing', 'Isolator & RCCB testing', '30-Day Guarantee']
  },
  {
    id: 'lighting',
    category: 'lighting',
    title: 'False Ceiling & Chandeliers',
    icon: '💡',
    price: '₹129',
    time: '30-45 mins',
    desc: 'Concealed LED panel lights, COB spotlights, chandelier hanging, profile lights, and decorative holders.',
    features: ['Moisture-safe LED drivers', 'True level alignment', 'Concealed wiring junction', '30-Day Guarantee']
  },
  {
    id: 'inverter',
    category: 'inverter',
    title: 'Inverter & Battery Setup',
    icon: '🔋',
    price: '₹249',
    time: '45-60 mins',
    desc: 'Inverter bypass switch, tubular battery distilled water check, terminal corrosion cleanup, backup wire setup.',
    features: ['Acid terminal gel coating', 'Backup load calculation', 'High-current copper lugs', '30-Day Guarantee']
  },
  {
    id: 'wiring',
    category: 'wiring',
    title: 'Full House Wiring & Earthing',
    icon: '🛡️',
    price: '₹499',
    time: '1-3 hours',
    desc: 'Conduit concealed wiring, copper earthing pit test, distribution board phase balance, leakage test.',
    features: ['Megger insulation test', 'ISI pure copper wire checks', 'Electric shock safety audit', '30-Day Guarantee']
  },
  {
    id: 'emergency',
    category: 'emergency',
    title: '24/7 Emergency Hazard Repair',
    icon: '🚨',
    price: '₹299',
    time: '15-25 min arrival',
    desc: 'Active sparking, burning odor, water entering light fitting, total power blackout, or neutral shock.',
    features: ['Priority dispatch in Lucknow', 'Main breaker isolation', 'Fire risk safety containment', 'Rapid pro on site']
  },
  {
    id: 'geyser',
    category: 'appliances',
    title: 'Geyser & AC Power Plug Wiring',
    icon: '🚿',
    price: '₹199',
    time: '30-45 mins',
    desc: 'Heavy 16A/25A power point wiring, geyser thermostat safety, AC outdoor isolator switch connection.',
    features: ['4.0 sq.mm heavy gauge copper', 'Dedicated breaker isolation', 'Water-resistant junction box', '30-Day Guarantee']
  }
];

const REVIEWS_DATA = [
  {
    name: 'Rajesh Verma',
    area: 'Gomti Nagar, Phase 2',
    rating: 5,
    text: 'Saved us during a midnight short circuit in our main meter box. The electrician arrived in 18 minutes flat, diagnosed the burnt neutral, and fixed it cleanly. Outstanding service!',
    service: 'Emergency Short Circuit'
  },
  {
    name: 'Sunita Tripathi',
    area: 'Indira Nagar, Sector 14',
    rating: 5,
    text: 'Bijli Guru AI gave us the exact reason why our ceiling fan was humming and running slow. Booked Rahul Sharma from the app — changed capacitor and greased bearings in 25 mins. Transparent rate of ₹149!',
    service: 'Ceiling Fan Repair'
  },
  {
    name: 'Mohd. Tariq',
    area: 'Hazratganj, Park Road',
    rating: 5,
    text: 'Very professional, polite, and carried ISI-certified modular switches. Replaced 3 burnt switchboards with proper copper earthing check. Highly recommended for all Lucknow residents.',
    service: 'Switchboard Replacement'
  },
  {
    name: 'Ananya Saxena',
    area: 'Aliganj, Sector B',
    rating: 5,
    text: 'Setup our heavy tubular inverter and bypass switch. Clean work, verified ID badge, and no surge pricing. Loved the live distance tracking on map!',
    service: 'Inverter Installation'
  }
];

const FAQ_DATA = [
  {
    q: 'How fast will an electrician arrive at my home in Lucknow?',
    a: 'Our certified master electricians are stationed across 12 zones in Lucknow. Average doorstep arrival is 20 to 30 minutes in Gomti Nagar, Hazratganj, Indira Nagar, Aliganj, and neighboring localities.'
  },
  {
    q: 'How does the Bijli Guru AI Assistant (Ollama) diagnose my problem?',
    a: 'Bijli Guru is powered by Ollama Local AI with an electrical fault-reasoning engine. It analyzes symptoms you describe (like fan humming, breaker tripping, or burnt odors), gives immediate safety cautions, provides standard Lucknow price estimates, and matches you with nearby pros.'
  },
  {
    q: 'Are your rates fixed or will the technician demand extra at the doorstep?',
    a: 'ElectroFix provides 100% upfront, transparent pricing. The service inspection and standard labor rates are fixed as shown in our rate card. Spare parts, if needed, are billed at MRP with authentic ISI-mark warranty.'
  },
  {
    q: 'What if the electrical issue recurs within a month?',
    a: 'Every repair comes with our signature 30-Day Free Service Guarantee. If the same issue happens again within 30 days, our technician will revisit and rectify it at zero labor charge.'
  },
  {
    q: 'Do you operate during late night emergency hours in Lucknow?',
    a: 'Yes! Our 24/7 Rapid Emergency Response team is active round the clock for dangerous electrical hazards like active sparking, burning plastic smells, water leakages near breakers, or total power failure.'
  }
];

const PACKAGES_DATA = [
  {
    id: 'pkg_essential',
    badge: 'Popular for Tenants & Flats',
    title: 'Essential Home Health Check',
    price: '₹399',
    period: 'One-Time Comprehensive Inspection',
    icon: '🔍',
    popular: false,
    desc: 'Complete inspection of all switches, sockets, fan capacitors and earth leakage across your home.',
    features: [
      '18-Point switchboard & terminal tightening',
      'Ceiling fan capacitor & bearing lubrication',
      'Main MCB breaker trip calibration test',
      'Phase & Neutral load balancing check',
      'Digital earth leakage safety report',
      '30-Day Free Re-Service Warranty'
    ]
  },
  {
    id: 'pkg_monsoon',
    badge: '⭐ Recommended for Lucknow Monsoons',
    title: 'Monsoon & Moisture Shield Audit',
    price: '₹699',
    period: 'Heavy Load & Rain Protection',
    icon: '🌧️',
    popular: true,
    desc: 'Protect against false ceiling water seepage, inverter corrosion, and high AC/Geyser loads.',
    features: [
      'Everything in Essential Checkup included',
      'Concealed false ceiling LED driver moisture audit',
      'Outdoor meter box & service cable weather sealing',
      'Inverter battery acid desulfation & water top-up',
      'Dedicated 16A AC & Geyser point insulation test',
      '60-Day Extended Warranty + Emergency Priority'
    ]
  },
  {
    id: 'pkg_annual',
    badge: '👑 Best Value for Homeowners',
    title: '365-Day Total Home AMC Plan',
    price: '₹1,499',
    period: 'Full 1-Year Comprehensive Coverage',
    icon: '🛡️',
    popular: false,
    desc: 'Zero-headache annual electrical protection for your family, home wiring, and expensive appliances.',
    features: [
      '2 Scheduled full-house comprehensive audits/yr',
      'Unlimited 24/7 priority emergency callouts (₹0 Visit Fee)',
      '15% Flat discount on all replacement parts & wires',
      'Free capacitor & switch replacements (up to 2/yr)',
      'Dedicated Senior Master Electrician assigned',
      'Full 1-Year Service & Workmanship Guarantee'
    ]
  }
];

const CASE_STUDIES_DATA = [
  {
    id: 'meter_box',
    title: 'Burnt Meter Box & Overheated Neutral',
    locality: 'Sector 14, Indira Nagar, Lucknow',
    time: 'Fixed in 35 mins',
    urgency: 'Active Sparking & Burning Smell',
    problem: 'An overloaded wooden distribution board with 4 spliced AC neutral wires started smoking and sparking at 11 PM during summer heatwaves.',
    solution: 'Safely isolated service line fuse. Installed modern IP65 fire-retardant DIN rail enclosure, 63A isolator switch, and a 30mA RCCB sensitive shock protector.',
    result: '100% compliant with electrical safety codes, eliminated wire heating, and restored power safely to all 3 AC units.',
    icon: '⚡',
    stats: '0V Neutral Leakage • 100% Fire Safe'
  },
  {
    id: 'neutral_shock',
    title: '48V Electric Tingling in Bathroom Taps',
    locality: 'Sector B, Aliganj, Lucknow',
    time: 'Fixed in 45 mins',
    urgency: 'Safety Shock Hazard',
    problem: 'Family members experienced sharp electric current shocks whenever touching wet bathroom steel faucets or the washing machine frame.',
    solution: 'Detected severed underground earthing wire corroded by soil salts. Installed a fresh chemical copper bonded earth rod with conductive bentonite compound.',
    result: 'Tap voltage dropped from 48V to 0.2V (completely safe). Earth pit resistance reduced to an optimal 1.8 Ohms.',
    icon: '🚿',
    stats: '1.8Ω Earth Resistance • Zero Shock'
  },
  {
    id: 'inverter_draining',
    title: 'Inverter Beeping Non-Stop & Rapid Battery Drain',
    locality: 'Vibhuti Khand, Gomti Nagar, Lucknow',
    time: 'Fixed in 30 mins',
    urgency: 'Power Cut Failure',
    problem: 'A 150Ah tubular inverter battery was discharging within 20 minutes during power outages, accompanied by a continuous overload alarm.',
    solution: 'Cleaned heavy lead sulfate corrosion from battery posts with neutralizer solution, replaced frayed 10 sq.mm battery cables, and installed an external manual bypass switch.',
    result: 'Restored backup runtime from 20 minutes back to 4.5 hours under standard 3-fan household load.',
    icon: '🔋',
    stats: '4.5 Hours Backup • Bypass Installed'
  }
];

const COMMERCIAL_SOLUTIONS_DATA = [
  {
    icon: '🚗',
    title: 'Certified EV Home & Society Charger Setup',
    desc: 'Fast, safe AC wallbox installation (3.3kW to 7.4kW) for Tata, MG, Ather, Ola, and Hyundai EVs. Dedicated MCB isolator, 4.0 sq.mm copper run, and dedicated earthing.',
    features: ['Dedicated Earth Pit Test', 'Heavy Armored Conduit Cable', 'Over-Voltage & Surge Arrester', 'Govt Discom / RWA Approval Ready']
  },
  {
    icon: '🏢',
    title: 'Apartment Societies & RWA Maintenance (AMC)',
    desc: 'Comprehensive electrical management for multi-story residential towers across Gomti Nagar Ext, Omaxe, Eldeco, and Shalimar townships.',
    features: ['Main Substation / Meter Room Audits', 'DG Generator AMF Panel Changeover', 'Common Lift & Corridor Phase Balance', 'Dedicated Society On-Call Electrician']
  },
  {
    icon: '🏬',
    title: 'Retail Stores, Cafes & Commercial Offices',
    desc: 'Custom electrical layouts, decorative track lighting, data server UPS cabling, and 3-Phase load balancing for Lucknow businesses.',
    features: ['3-Phase Industrial Distribution', 'Precision Commercial Load Calculation', 'Zero-Downtime Weekend Execution', 'GST Invoicing with Compliance Docs']
  }
];

const TECHNICIANS_SHOWCASE_DATA = [
  {
    name: 'Manoj Tiwari',
    role: 'Senior Master Electrician & Tripping Specialist',
    exp: '12+ Years Experience',
    area: 'Gomti Nagar & Hazratganj',
    rating: 4.98,
    jobs: '1,420+ Jobs Completed',
    badge: 'Govt Certified A-Grade',
    avatarText: 'MT',
    skills: ['MCB Tripping', '3-Phase Phase Balance', 'Emergency Hazards']
  },
  {
    name: 'Rahul Sharma',
    role: 'Precision Appliance & Inverter Engineer',
    exp: '9+ Years Experience',
    area: 'Indira Nagar & Mahanagar',
    rating: 4.95,
    jobs: '980+ Jobs Completed',
    badge: 'Appliance Specialist',
    avatarText: 'RS',
    skills: ['Inverter Systems', 'Ceiling Fan Bearings', 'Modular Boards']
  },
  {
    name: 'Amit Verma',
    role: 'Conduit Wiring & False Ceiling Master',
    exp: '11+ Years Experience',
    area: 'Aliganj & Vikas Nagar',
    rating: 4.93,
    jobs: '1,160+ Jobs Completed',
    badge: 'Conduit Master',
    avatarText: 'AV',
    skills: ['False Ceiling Lighting', 'Concealed Conduit', 'Earth Pits']
  },
  {
    name: 'Vikram Singh',
    role: 'Rapid Emergency Response Captain',
    exp: '8+ Years Experience',
    area: 'Alambagh & Ashiyana',
    rating: 4.97,
    jobs: '890+ Jobs Completed',
    badge: 'Rapid SOS Certified',
    avatarText: 'VS',
    skills: ['Short Circuit Containment', 'Heavy Power Plugs', 'Live Testing']
  }
];

const WHY_US_COMPARISON_DATA = [
  {
    category: 'speed',
    feature: 'Response & Arrival Time',
    localMistri: 'Unpredictable "Bhaiya aa rahe hain" (2 to 6 hours or next day)',
    electroFix: 'Guaranteed 20-30 min arrival with live GPS tracking across Lucknow',
    icon: '⏱️'
  },
  {
    category: 'pricing',
    feature: 'Pricing Transparency',
    localMistri: 'Arbitrary verbal quote, surprise extras & doorstep bargaining',
    electroFix: '100% upfront published rate card from ₹99 with digital GST invoice',
    icon: '💰'
  },
  {
    category: 'safety',
    feature: 'Safety Gear & Diagnostics',
    localMistri: 'Bare hands, rusted neon tester, no True-RMS multimeter',
    electroFix: 'German VDE 1,000V insulated handtools, True-RMS digital clamp & earth testers',
    icon: '🧰'
  },
  {
    category: 'safety',
    feature: 'Wire & Spare Quality',
    localMistri: 'Local non-ISI duplicate scrap wires & cheap unbranded switches',
    electroFix: '100% genuine ISI-marked electrolytic copper (Havells, Polycab, Anchor)',
    icon: '🔌'
  },
  {
    category: 'pricing',
    feature: 'Post-Service Warranty',
    localMistri: 'Zero warranty — phone switched off if switchboard sparks again',
    electroFix: '30-Day Hassle-Free Re-Service Warranty backed by app guarantee',
    icon: '🛡️'
  },
  {
    category: 'speed',
    feature: 'Identity & Home Security',
    localMistri: 'Unknown stranger entering your private home & bedrooms',
    electroFix: '100% police verified, uniformed master technicians with photo ID badges',
    icon: '👮'
  },
  {
    category: 'speed',
    feature: 'AI Instant Diagnostic',
    localMistri: 'None — guesses fault by trial and error at your cost',
    electroFix: 'Bijli Guru (Ollama) instant voice/text triage with fault reasoning',
    icon: '🤖'
  },
  {
    category: 'safety',
    feature: 'Property Damage Cover',
    localMistri: 'Zero responsibility if appliance burns or wall is damaged',
    electroFix: 'Complimentary ₹10,000 property protection cover on every visit',
    icon: '🔒'
  }
];

const SEASONAL_CHECKLIST_DATA = {
  monsoon: {
    title: 'Monsoon & High Humidity Shield',
    season: 'Rainy Season (July - September)',
    icon: '🌧️',
    summary: 'Water seepage into conduits and high humidity cause frequent earth leakages, tap shocks, and wall damp sparking in Lucknow homes.',
    items: [
      { id: 'monsoon_rccb', label: '30mA RCCB / ELCB shock trip breaker tested and operational', risk: 'High Shock Risk' },
      { id: 'monsoon_falseceiling', label: 'False ceiling concealed LED drivers inspected for moisture seepage', risk: 'Ceiling Fire Risk' },
      { id: 'monsoon_outdoorbox', label: 'Outdoor electricity meter box & main cable weather-sealed', risk: 'Short Circuit' },
      { id: 'monsoon_motorwaterproof', label: 'Submersible water pump starter and conduit waterproofed', risk: 'Motor Burnout' }
    ]
  },
  summer: {
    title: 'Extreme Summer Peak AC & Phase Load',
    season: 'Peak Summer (April - June)',
    icon: '☀️',
    summary: 'Continuous 1.5/2 Ton AC loads overheat main neutrals, melt wooden boards, and drain inverter tubular batteries rapidly.',
    items: [
      { id: 'summer_acwire', label: 'Dedicated 4.0 sq.mm heavy gauge copper wiring for all AC units', risk: 'Wire Melting Risk' },
      { id: 'summer_phasebalance', label: '3-Phase load balanced across meter phases to prevent neutral burnout', risk: 'Phase Drop' },
      { id: 'summer_inverterwater', label: 'Tubular inverter battery distilled water top-up & terminal grease applied', risk: 'Battery Failure' },
      { id: 'summer_exhaust', label: 'Kitchen & generator room heat exhaust fans running at optimal RPM', risk: 'Overheating' }
    ]
  },
  winter: {
    title: 'Winter High-Current Geyser & Heater Safety',
    season: 'Winter Season (November - February)',
    icon: '❄️',
    summary: '2kW to 3kW water geysers and quartz room heaters place intense localized load on 16A modular sockets.',
    items: [
      { id: 'winter_geysersocket', label: 'Heavy-duty 16A/25A porcelain modular socket used for bathroom geyser', risk: 'Plug Charring' },
      { id: 'winter_taptingling', label: 'Zero voltage tingling shock on wet bathroom faucets and metallic pipes', risk: 'Severe Shock' },
      { id: 'winter_heaterwire', label: 'Room heaters connected directly to wall sockets without cheap multi-plugs', risk: 'Fire Hazard' }
    ]
  },
  festive: {
    title: 'Festive & Decorative Lighting Load',
    season: 'Festival Season (Diwali & New Year)',
    icon: '🪔',
    summary: 'Dozens of fairy lights and profile lights connected to a single socket can exceed rated breaker amperes.',
    items: [
      { id: 'festive_multiplug', label: 'Eliminated overloaded daisy-chained multi-plug adapters', risk: 'Socket Overload' },
      { id: 'festive_outdoordriver', label: 'Outdoor facade fairy light SMPS drivers housed in IP65 weather boxes', risk: 'Rain Spark' }
    ]
  }
};

const GOLDEN_GUARANTEES_DATA = [
  {
    icon: '⏱️',
    title: '25-Min Doorstep Arrival or ₹100 Off',
    subtitle: 'Lucknow Zone Punctuality Guarantee',
    desc: 'If our certified master electrician arrives later than the confirmed arrival ETA window, we credit ₹100 straight toward your service bill.',
    badge: 'Punctuality Promise'
  },
  {
    icon: '💰',
    title: '100% Upfront Rate Card Guarantee',
    subtitle: 'Zero Hidden Surcharges',
    desc: 'You know the exact labor charge before any screw is turned. No surprise travel fees, no rain surcharges, and no arbitrary doorstep bargaining.',
    badge: 'Transparent Pricing'
  },
  {
    icon: '🛡️',
    title: '30-Day Hassle-Free Re-Service Warranty',
    subtitle: '100% Free Rework Assurance',
    desc: 'If the repaired switch, fan, breaker, or wiring recurs within 30 days, we dispatch a senior pro to fix it at zero extra labor charge.',
    badge: 'Risk-Free Repair'
  },
  {
    icon: '🔌',
    title: '100% Genuine ISI Mark Spares Guarantee',
    subtitle: 'Authentic Branded Copper & Accessories',
    desc: 'We strictly reject counterfeit roadside wires. All replacement switches, MCBs, and cables come with authentic manufacturer ISI seals.',
    badge: 'Pure Copper ISI'
  },
  {
    icon: '🧹',
    title: 'Zero-Mess & Post-Repair Clean-Up',
    subtitle: 'Spotless Living Space Guarantee',
    desc: 'Our technician vacuums and cleans wire clippings, plaster dust, and old packaging before presenting the completion certificate.',
    badge: 'Spotless Home'
  },
  {
    icon: '👮',
    title: '100% Police Verified & Insured Pros',
    subtitle: 'Family & Property Safety First',
    desc: 'Every electrician has cleared mandatory police verification and carries an official ID card. Plus, ₹10,000 property protection cover on every job.',
    badge: 'Verified & Insured'
  }
];

const LUCKNOW_HELPLINE_DATA = [
  {
    division: 'MVVNL / UPPCL Official Electricity Helpline',
    phone: '1912',
    timing: '24x7 Round The Clock',
    type: 'Government Discom Power Grid Help',
    icon: '🏛️',
    desc: 'Toll-free government helpline for Lucknow area power outages, transformer sparks, and grid supply issues.'
  },
  {
    division: 'ElectroFix Emergency SOS Flying Squad',
    phone: '+91 98123 45678',
    timing: '24x7 Rapid Doorstep Dispatch',
    type: 'Private Certified Emergency Electricians',
    icon: '🚨',
    desc: 'Immediate dispatch for active sparking, burning odor, meter board flames, or internal house blackouts across Lucknow.'
  },
  {
    division: 'Gomti Nagar & Trans-Gomti Division Help',
    phone: '0522-2720101',
    timing: '8:00 AM - 10:00 PM',
    type: 'Vibhuti Khand, Patrakarpuram, Gomti Ext',
    icon: '⚡',
    desc: 'Direct substation line for Gomti Nagar phases 1 & 2, Indira Nagar, and Shaheed Path sectors.'
  },
  {
    division: 'Hazratganj & Central Lucknow Substation',
    phone: '0522-2620202',
    timing: '8:00 AM - 10:00 PM',
    type: 'Hazratganj, Raj Bhavan, Park Road',
    icon: '🏢',
    desc: 'Direct dispatch assistance for central Lucknow heritage and commercial zones.'
  },
  {
    division: 'Aliganj & North Lucknow Substation',
    phone: '0522-2320303',
    timing: '8:00 AM - 10:00 PM',
    type: 'Aliganj, Kapoorthala, Jankipuram',
    icon: '🔌',
    desc: 'Direct dispatch assistance for North Lucknow residential colonies and apartments.'
  },
  {
    division: 'Alambagh & South Lucknow Substation',
    phone: '0522-2420404',
    timing: '8:00 AM - 10:00 PM',
    type: 'Alambagh, Ashiyana, Krishna Nagar',
    icon: '🚆',
    desc: 'Direct substation assistance for South Lucknow, Kanpur Road, and VIP Road colonies.'
  }
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
  const [selectedArea, setSelectedArea] = useState('gomti_nagar');
  const [electricians, setElectricians] = useState([]);
  const [loading, setLoading] = useState(false);
  const [selectedElectrician, setSelectedElectrician] = useState(null);

  // New UI & Interaction States
  const [heroSearch, setHeroSearch] = useState('');
  const [serviceCategory, setServiceCategory] = useState('all');
  const [activeFaq, setActiveFaq] = useState(null);

  // Rate Calculator State
  const [calcItems, setCalcItems] = useState({
    fans: 1,
    switches: 2,
    mcb: 0,
    inverter: 0,
    wiring: 0,
    geyser: 0
  });

  // 1. Electrical Risk Self-Audit State
  const [riskAnswers, setRiskAnswers] = useState({
    sparking: 0,
    shocks: 0,
    tripping: 0
  });
  const riskScore = riskAnswers.sparking + riskAnswers.shocks + riskAnswers.tripping;
  const riskLevel = riskScore <= 1 ? 'safe' : riskScore <= 3 ? 'moderate' : 'critical';

  // 2. Energy & Electricity Bill Estimator State
  const [energyAcHours, setEnergyAcHours] = useState(6);
  const [energyGeyserMins, setEnergyGeyserMins] = useState(30);
  const [energyFansCount, setEnergyFansCount] = useState(3);
  const [energyBldcToggle, setEnergyBldcToggle] = useState(false);

  const acKwhMonthly = energyAcHours * 1.5 * 30;
  const geyserKwhMonthly = (energyGeyserMins / 60) * 2.0 * 30;
  const fansKwhMonthly = energyFansCount * (energyBldcToggle ? 0.028 : 0.075) * 12 * 30;
  const regularFansKwhMonthly = energyFansCount * 0.075 * 12 * 30;
  const bldcSavingsMonthly = Math.round((regularFansKwhMonthly - (energyFansCount * 0.028 * 12 * 30)) * 6.5);
  const totalUnitsMonthly = Math.round(acKwhMonthly + geyserKwhMonthly + fansKwhMonthly);
  const estimatedBillMonthly = Math.round(totalUnitsMonthly * 6.5);

  // 3. Case Studies Active Tab State
  const [activeCaseStudy, setActiveCaseStudy] = useState(0);

  // 4. Why Us Comparison Active Tab
  const [whyUsTab, setWhyUsTab] = useState('all');

  // 5. Seasonal Checklist Active Tab & Items
  const [seasonalTab, setSeasonalTab] = useState('monsoon');
  const [safetyChecklist, setSafetyChecklist] = useState({
    monsoon_rccb: true,
    monsoon_falseceiling: false,
    monsoon_outdoorbox: true,
    monsoon_motorwaterproof: false,
    summer_acwire: true,
    summer_phasebalance: false,
    summer_inverterwater: true,
    summer_exhaust: false,
    winter_geysersocket: true,
    winter_taptingling: true,
    winter_heaterwire: false,
    festive_multiplug: true,
    festive_outdoordriver: false
  });

  const toggleChecklistItem = (itemId) => {
    setSafetyChecklist((prev) => ({
      ...prev,
      [itemId]: !prev[itemId]
    }));
  };

  const activeSeasonItems = SEASONAL_CHECKLIST_DATA[seasonalTab]?.items || [];
  const checkedSeasonCount = activeSeasonItems.filter((it) => safetyChecklist[it.id]).length;
  const seasonalScorePct = activeSeasonItems.length > 0
    ? Math.round((checkedSeasonCount / activeSeasonItems.length) * 100)
    : 100;

  // AI Assistant trigger state
  const [robotQuery, setRobotQuery] = useState('');
  const [isRobotForceOpen, setIsRobotForceOpen] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  // Contact Form & AI Agent Email State
  const [contactForm, setContactForm] = useState({
    name: '',
    email: '',
    phone: '',
    area: 'Gomti Nagar, Lucknow',
    subject: '',
    message: '',
  });
  const [contactAiLoading, setContactAiLoading] = useState(false);
  const [contactAiResult, setContactAiResult] = useState(null);
  const [contactSending, setContactSending] = useState(false);
  const [contactSuccess, setContactSuccess] = useState(null);
  const [contactError, setContactError] = useState('');

  // Booking Modal State
  const [bookingModal, setBookingModal] = useState({
    isOpen: false,
    electrician: null,
    service: 'Electrical Repair',
    customerName: '',
    customerPhone: '',
    customerAddress: '',
    timeSlot: 'Within 30 mins (Immediate)',
    notes: '',
    successReference: null
  });

  const mapContainerRef = useRef(null);
  const mapInstanceRef = useRef(null);
  const markersGroupRef = useRef(null);
  const customerMarkerRef = useRef(null);

  // On mount: detect location from browser
  useEffect(() => {
    detectLocationFromBrowser();
  }, []);

  // Geocoding Coordinates into Lucknow locality
  const detectAreaFromCoords = async (lat, lng) => {
    let areaName = 'Lucknow';
    let areaKey = 'gomti_nagar';

    try {
      const res = await api.get(`/geocode?latitude=${lat}&longitude=${lng}`);
      if (res.data && res.data.success) {
        areaName = res.data.formatted || (res.data.area + ', Lucknow');
        areaKey = res.data.area_key || 'gomti_nagar';
      }
    } catch (err) {
      const closest = findClosestLucknowArea(lat, lng);
      areaName = `${closest.name}, Lucknow`;
      areaKey = closest.id;
    }

    setCustomerLocation({
      latitude: lat,
      longitude: lng,
      areaName: areaName
    });
    setSelectedArea(areaKey);
    fetchNearbyElectricians(lat, lng);
  };

  // Browser Geolocation
  const detectLocationFromBrowser = () => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          detectAreaFromCoords(pos.coords.latitude, pos.coords.longitude);
        },
        () => {
          fetchNearbyElectricians(customerLocation.latitude, customerLocation.longitude);
        },
        { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
      );
    } else {
      fetchNearbyElectricians(customerLocation.latitude, customerLocation.longitude);
    }
  };

  // Fetch nearby electricians from API
  const fetchNearbyElectricians = (lat, lng) => {
    setLoading(true);
    api.get(`/electricians/nearby?latitude=${lat}&longitude=${lng}`)
      .then((res) => {
        if (res.data && res.data.success) {
          setElectricians(res.data.data);
        }
      })
      .catch((err) => {
        console.error('Failed to fetch nearby electricians:', err);
      })
      .finally(() => {
        setLoading(false);
      });
  };

  // Quick Area Change
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
    fetchNearbyElectricians(area.lat, area.lng);
  };

  // Leaflet Map Initialization
  useEffect(() => {
    if (!customerLocation || !mapContainerRef.current) return;

    if (!mapInstanceRef.current) {
      const map = L.map(mapContainerRef.current).setView(
        [customerLocation.latitude, customerLocation.longitude],
        13
      );

      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
      }).addTo(map);

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

    // Customer Marker
    const customerIcon = L.divIcon({
      className: 'custom-customer-icon',
      html: `
        <div style="position: relative; display: flex; flex-direction: column; align-items: center; cursor: grab;">
          <div style="background: linear-gradient(135deg, #1d4ed8, #2563eb); color: #ffffff; padding: 5px 12px; border-radius: 9999px; font-size: 12px; font-weight: 800; white-space: nowrap; box-shadow: 0 4px 14px rgba(37,99,235,0.45); border: 2px solid #ffffff; display: flex; align-items: center; gap: 6px;">
            <span>🏠</span>
            <span>You Live Here</span>
          </div>
          <div style="width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 8px solid #2563eb; margin-top: -1px;"></div>
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
      <div style="font-family: inherit; font-size: 13px; text-align: center; padding: 4px; min-width: 180px;">
        <strong style="color: #2563eb; font-size: 14px;">🏠 You Live Here</strong><br>
        <span style="color: #0f172a; font-weight: 700;">${customerLocation.areaName}</span><br>
        <span style="color: #64748b; font-size: 11px;">(Drag this pin to any street in Lucknow)</span>
      </div>
    `);

    customerMarker.on('dragend', (e) => {
      const position = e.target.getLatLng();
      detectAreaFromCoords(position.lat, position.lng);
    });

    customerMarkerRef.current = customerMarker;

    // Electricians Markers
    electricians.forEach((elec) => {
      const elecIcon = L.divIcon({
        className: 'custom-elec-icon',
        html: `
          <div style="width: 32px; height: 32px; background-color: #f59e0b; color: #ffffff; border-radius: 50%; border: 2px solid #ffffff; box-shadow: 0 2px 8px rgba(245,158,11,0.6); display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: bold; cursor: pointer;">
            ⚡
          </div>
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
          <strong style="color: #2563eb;">Distance: ${elec.distance}</strong><br>
          <span style="font-size: 12px; color: #10b981; font-weight: 600;">📞 ${elec.phone}</span>
        </div>
      `);

      marker.on('click', () => {
        setSelectedElectrician(elec);
      });
    });

    map.setView([customerLocation.latitude, customerLocation.longitude], 13);
  }, [customerLocation, electricians]);

  // Select Electrician from Card
  const handleSelectElectrician = (elec) => {
    setSelectedElectrician(elec);
    if (mapInstanceRef.current && elec.latitude && elec.longitude) {
      mapInstanceRef.current.flyTo([elec.latitude, elec.longitude], 15, { duration: 0.8 });
    }
  };

  // Open AI Diagnostic with custom issue
  const triggerAiDiagnostic = (queryText) => {
    setRobotQuery(queryText);
    setIsRobotForceOpen(true);
  };

  // Open Booking Modal
  const openBookingModal = (electrician = null, serviceName = 'Electrical Repair') => {
    setBookingModal({
      isOpen: true,
      electrician: electrician,
      service: serviceName,
      customerName: '',
      customerPhone: '',
      customerAddress: customerLocation.areaName,
      timeSlot: 'Within 30 mins (Immediate)',
      notes: '',
      successReference: null
    });
  };

  // Submit Booking Form
  const handleSubmitBooking = async (e) => {
    e.preventDefault();
    const elecId = bookingModal.electrician ? bookingModal.electrician.id : (electricians[0] ? electricians[0].id : 1);

    try {
      const res = await api.post('/ai-agent/tools/create-booking', {
        electrician_id: elecId,
        customer_name: bookingModal.customerName || 'Customer',
        customer_phone: bookingModal.customerPhone || '+91 9812340001',
        customer_address: bookingModal.customerAddress || customerLocation.areaName,
        service_type: bookingModal.service,
        time_slot: bookingModal.timeSlot,
        notes: bookingModal.notes
      });

      if (res.data && res.data.booking_reference) {
        setBookingModal((prev) => ({
          ...prev,
          successReference: res.data.booking_reference
        }));
      }
    } catch (err) {
      console.warn('Backend booking error, using simulated reference:', err);
      const fakeRef = `LKO-${Math.floor(1000 + Math.random() * 9000)}`;
      setBookingModal((prev) => ({
        ...prev,
        successReference: fakeRef
      }));
    }
  };

  // Contact Email AI Handlers
  const handleAiDraftContact = async () => {
    if (!contactForm.message.trim()) {
      setContactError('Please describe your electrical problem first so our AI agent can diagnose it.');
      return;
    }
    setContactError('');
    setContactAiLoading(true);
    try {
      const res = await api.post('/ai-agent/ai-draft-contact', {
        problem_description: contactForm.message,
        area: contactForm.area || 'Lucknow',
      });
      if (res.data && res.data.success) {
        setContactAiResult(res.data);
        if (res.data.suggested_subject) {
          setContactForm((prev) => ({
            ...prev,
            subject: prev.subject || res.data.suggested_subject,
          }));
        }
      }
    } catch (err) {
      console.warn('AI contact draft error, using local advisory:', err);
      setContactAiResult({
        ai_priority: 'HIGH',
        ai_diagnosis: 'Doorstep electrical diagnostic logged for Lucknow area. Verified master electrician dispatch assigned.',
        ai_recommended_service: 'General Electrical Fix & Safety Audit',
        ai_estimated_cost: '₹149 - ₹249',
        safety_advisory: 'Ensure main breaker is isolated if you notice sparking or burning smell.'
      });
    } finally {
      setContactAiLoading(false);
    }
  };

  const handleSendContactEmail = async (e) => {
    if (e) e.preventDefault();
    if (!contactForm.name.trim() || !contactForm.email.trim() || !contactForm.message.trim()) {
      setContactError('Please fill in your Name, Email Address, and Problem description.');
      return;
    }
    setContactError('');
    setContactSending(true);
    try {
      const res = await api.post('/ai-agent/contact-email', {
        name: contactForm.name,
        email: contactForm.email,
        phone: contactForm.phone,
        area: contactForm.area,
        subject: contactForm.subject || contactAiResult?.suggested_subject || 'Electrical Service Inquiry',
        message: contactForm.message,
        ai_diagnosis: contactAiResult?.ai_diagnosis || null,
        ai_priority: contactAiResult?.ai_priority || null,
      });
      if (res.data && res.data.success) {
        setContactSuccess(res.data);
      } else {
        setContactError(res.data?.message || 'Failed to send contact email. Please try again.');
      }
    } catch (err) {
      console.error('Contact email error:', err);
      setContactError('Failed to send contact email. Please check your network or call 24/7 hotline.');
    } finally {
      setContactSending(false);
    }
  };

  const resetContactForm = () => {
    setContactForm({
      name: '',
      email: '',
      phone: '',
      area: 'Gomti Nagar, Lucknow',
      subject: '',
      message: '',
    });
    setContactAiResult(null);
    setContactSuccess(null);
    setContactError('');
  };

  // Rate Calculator Math
  const calcSubtotal =
    calcItems.fans * 149 +
    calcItems.switches * 99 +
    calcItems.mcb * 199 +
    calcItems.inverter * 249 +
    calcItems.wiring * 499 +
    calcItems.geyser * 199;

  // Filtered Services List
  const filteredServices = serviceCategory === 'all'
    ? SERVICES_CATALOG
    : SERVICES_CATALOG.filter((s) => s.category === serviceCategory);

  return (
    <div>
      {/* 1. Top Emergency Dispatch Banner */}
      <div className="top-emergency-banner">
        <span className="pulsing-dot"></span>
        <span>⚡ 24/7 Lucknow Doorstep Electrician Fleet • Average Arrival: <strong>20-25 Mins</strong></span>
        <span>•</span>
        <span>Emergency Hotline:</span>
        <a href="tel:+919812345678" className="hotline-link">
          📞 +91 98123 45678
        </a>
      </div>

      {/* 2. Site Navbar */}
      <nav className={`site-navbar ${mobileMenuOpen ? 'nav-expanded' : ''}`}>
        <div className="navbar-inner">
          <a href="#" className="nav-brand">
            <div className="brand-icon">⚡</div>
            <div className="brand-text">
              <span className="brand-title">ElectroFix</span>
              <span className="brand-subtitle">Lucknow Electricians</span>
            </div>
          </a>

          {/* Clean 4-Option Navigation */}
          <ul className="nav-menu">
            <li><a href="#services" className="nav-link">Services</a></li>
            <li><a href="#how-it-works" className="nav-link">How It Works</a></li>
            <li><a href="#why-us" className="nav-link">Why Us</a></li>
            <li><a href="#contact" className="nav-link">Contact</a></li>
          </ul>

          <div className="nav-actions">
            <button
              type="button"
              className="btn-ai-trigger"
              onClick={() => setIsRobotForceOpen(true)}
              title="Chat with Bijli Guru (AI Powered)"
            >
              <span className="ai-pulse-dot"></span>
              <span>🤖 Bijli Guru AI</span>
            </button>
            <button
              type="button"
              className="btn-book-cta"
              onClick={() => openBookingModal(null, 'Doorstep Electrical Service')}
            >
              <span>⚡</span> Book Visit
            </button>
            <button
              type="button"
              className="btn-mobile-toggle"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              aria-label="Toggle navigation menu"
            >
              {mobileMenuOpen ? '✕' : '☰'}
            </button>
          </div>
        </div>

        {/* Mobile Navigation Dropdown */}
        {mobileMenuOpen && (
          <div className="nav-mobile-dropdown">
            <div className="nav-mobile-links">
              <a href="#services" className="nav-mobile-link" onClick={() => setMobileMenuOpen(false)}>
                <span className="mobile-link-icon">⚡</span>
                <span className="mobile-link-text">Services & Rates</span>
              </a>
              <a href="#how-it-works" className="nav-mobile-link" onClick={() => setMobileMenuOpen(false)}>
                <span className="mobile-link-icon">⚙️</span>
                <span className="mobile-link-text">How It Works</span>
              </a>
              <a href="#why-us" className="nav-mobile-link" onClick={() => setMobileMenuOpen(false)}>
                <span className="mobile-link-icon">🛡️</span>
                <span className="mobile-link-text">Why Choose Us</span>
              </a>
              <a href="#contact" className="nav-mobile-link" onClick={() => setMobileMenuOpen(false)}>
                <span className="mobile-link-icon">✉️</span>
                <span className="mobile-link-text">Contact & Email Support</span>
              </a>
            </div>
            <div className="nav-mobile-cta">
              <a href="tel:+919812345678" className="btn-mobile-hotline">
                📞 24/7 Helpline: +91 98123 45678
              </a>
            </div>
          </div>
        )}
      </nav>

      {/* 3. Hero Section */}
      <header className="hero-section">
        <div className="hero-content">
          <div className="hero-pill-badge">
            <span className="badge-icon">⚡</span>
            <span>Verified Masters • 20-30 Min Arrival Across Lucknow</span>
          </div>

          <h1 className="hero-title">
            Lucknow's #1 Certified On-Demand <br />
            <span className="highlight-blue">Electricians</span> &{' '}
            <span className="highlight-amber">AI Diagnostic</span>
          </h1>

          <p className="hero-subtitle">
            Police-verified, licensed master electricians arriving at your doorstep in Gomti Nagar,
            Hazratganj, Indira Nagar & all Lucknow zones. Fixed upfront pricing with a 30-day warranty.
          </p>

          {/* Search Box */}
          <div className="hero-search-box">
            <span style={{ fontSize: '18px' }}>🔍</span>
            <input
              type="text"
              value={heroSearch}
              onChange={(e) => setHeroSearch(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && triggerAiDiagnostic(heroSearch || 'Help me diagnose electrical issue')}
              placeholder="What needs fixing? (e.g. Fan humming, MCB tripping, Switch burning smell...)"
            />
            <button
              type="button"
              onClick={() => triggerAiDiagnostic(heroSearch || 'Ceiling fan humming and running slow')}
            >
              <span>🤖</span> Diagnose with AI
            </button>
          </div>

          {/* Quick Tag Chips */}
          <div className="hero-tag-chips">
            <span className="tag-label">Popular Diagnostics:</span>
            <button type="button" className="hero-tag-chip" onClick={() => triggerAiDiagnostic('Fan kharab hai, humming sound aa rahi hai')}>
              🌀 Fan Humming (₹149)
            </button>
            <button type="button" className="hero-tag-chip" onClick={() => triggerAiDiagnostic('Switchboard se spark aa raha hai, burning smell hai')}>
              ⚠️ Sparking Socket (₹99)
            </button>
            <button type="button" className="hero-tag-chip" onClick={() => triggerAiDiagnostic('MCB baar baar trip ho rahi hai jab AC on karte hain')}>
              🔌 MCB Tripping (₹199)
            </button>
            <button type="button" className="hero-tag-chip" onClick={() => triggerAiDiagnostic('Inverter battery backup nahi de rahi')}>
              🔋 Inverter Setup (₹249)
            </button>
            <button type="button" className="hero-tag-chip" onClick={() => triggerAiDiagnostic('Full house wiring test aur earthing audit')}>
              🛡️ Earthing Audit (₹499)
            </button>
          </div>

          {/* Stats Grid */}
          <div className="hero-stats-grid">
            <div className="hero-stat-card">
              <div className="hero-stat-icon">⏱️</div>
              <div className="hero-stat-value">20-25 Mins</div>
              <div className="hero-stat-label">Doorstep Arrival</div>
            </div>
            <div className="hero-stat-card">
              <div className="hero-stat-icon">🛡️</div>
              <div className="hero-stat-value">30 Days</div>
              <div className="hero-stat-label">Free Warranty</div>
            </div>
            <div className="hero-stat-card">
              <div className="hero-stat-icon">💰</div>
              <div className="hero-stat-value">From ₹99</div>
              <div className="hero-stat-label">Transparent Rates</div>
            </div>
            <div className="hero-stat-card">
              <div className="hero-stat-icon">⭐</div>
              <div className="hero-stat-value">4.9 / 5.0</div>
              <div className="hero-stat-label">14,200+ Homes Fixed</div>
            </div>
            <div className="hero-stat-card">
              <div className="hero-stat-icon">👮</div>
              <div className="hero-stat-value">100% Verified</div>
              <div className="hero-stat-label">Police Checked Pros</div>
            </div>
          </div>
        </div>
      </header>

      {/* 4. Emergency Hazard Strip */}
      <section className="emergency-alert-section">
        <div className="emergency-alert-card">
          <div className="emergency-alert-info">
            <div className="alert-hazard-icon">⚠️</div>
            <div>
              <h3 className="alert-heading">Active Sparks, Burning Smell or Sudden Power Blackout?</h3>
              <p className="alert-desc">
                Immediately switch off your main distribution MCB breaker. Do NOT touch exposed wires.
                Our emergency rapid-response fleet reaches any Lucknow address in 15 to 25 minutes.
              </p>
            </div>
          </div>
          <div className="emergency-actions-group">
            <a href="tel:+919812345678" className="btn-emergency-call">
              📞 Call Emergency SOS
            </a>
            <button
              type="button"
              className="btn-emergency-ai"
              onClick={() => triggerAiDiagnostic('Emergency: Switchboard se dhuwan aur spark aa raha hai!')}
            >
              🚨 Report Hazard
            </button>
          </div>
        </div>
      </section>

      {/* 4.1 About ElectroFix Lucknow Section */}
      <section id="about" className="section-container about-section">
        <div className="section-header-centered">
          <span className="section-tag">About ElectroFix</span>
          <h2 className="section-title">Lucknow's Most Trusted Electrical Service Network</h2>
          <p className="section-subtitle">
            Founded with a singular mission: To bring transparency, safety, and certified master workmanship to every home across Gomti Nagar, Hazratganj, Indira Nagar, and all Lucknow zones.
          </p>
        </div>

        <div className="about-main-grid">
          {/* Left Column: Story & Lucknow Heritage */}
          <div className="about-story-col">
            <div className="about-story-badge">
              <span>🏛️</span>
              <span>Rooted in Lucknow • Vibhuti Khand, Gomti Nagar HQ</span>
            </div>
            <h3 className="about-story-title">
              No More Guesswork. No More Late Mistris. No More Arbitrary Pricing.
            </h3>
            <p className="about-story-text">
              For decades, families across Lucknow were left stranded when facing electrical emergencies: waiting 3 to 5 hours for an unverified roadside mistri who carried rusted tools, quoted arbitrary prices on the spot, and disappeared the moment an issue recurred.
            </p>
            <p className="about-story-text">
              <strong>ElectroFix was founded right here in the City of Nawabs to solve this forever.</strong> We built a unified network of 52+ licensed, police-screened master electricians equipped with German VDE 1,000V insulated handtools, True-RMS multimeters, and earth-resistance loop testers.
            </p>
            <p className="about-story-text">
              From historic kothis in Hazratganj to multi-story flats in Gomti Nagar Extension and bustling lanes in Chowk, our rapid response units reach your doorstep in <strong>20 to 25 minutes</strong>.
            </p>

            <div className="about-features-checks">
              <div className="about-check-item">
                <span className="about-check-icon">✓</span>
                <div>
                  <strong>100% Police Verified & Screened:</strong>
                  <span> Validated photo ID, background checks, and state electrical licensing.</span>
                </div>
              </div>
              <div className="about-check-item">
                <span className="about-check-icon">✓</span>
                <div>
                  <strong>Zero Counterfeit Wires Guarantee:</strong>
                  <span> Only authentic ISI-grade electrolytic copper and fire-retardant accessories.</span>
                </div>
              </div>
              <div className="about-check-item">
                <span className="about-check-icon">✓</span>
                <div>
                  <strong>30-Day Free Re-Service Warranty:</strong>
                  <span> Every job is logged digitally with a 30-day rework warranty.</span>
                </div>
              </div>
              <div className="about-check-item">
                <span className="about-check-icon">✓</span>
                <div>
                  <strong>Bijli Guru (बिजली गुरु) AI Diagnostic:</strong>
                  <span> Instant honest triage in Hindi & English powered by local AI before any pro is booked.</span>
                </div>
              </div>
            </div>
          </div>

          {/* Right Column: Key Metrics & HQ Trust Card */}
          <div className="about-stats-col">
            <div className="about-stats-card-grid">
              <div className="about-stat-box">
                <div className="about-stat-num">18,500+</div>
                <div className="about-stat-label">Lucknow Homes Safeguarded</div>
                <div className="about-stat-sub">Across 12 City Zones</div>
              </div>
              <div className="about-stat-box">
                <div className="about-stat-num">52+</div>
                <div className="about-stat-label">Licensed Master Pros</div>
                <div className="about-stat-sub">Govt A-Grade Certified</div>
              </div>
              <div className="about-stat-box">
                <div className="about-stat-num">20-25m</div>
                <div className="about-stat-label">Average Doorstep ETA</div>
                <div className="about-stat-sub">Live GPS-Assigned Dispatch</div>
              </div>
              <div className="about-stat-box">
                <div className="about-stat-num">4.92 / 5</div>
                <div className="about-stat-label">Customer Satisfaction</div>
                <div className="about-stat-sub">Based on 6,400+ Verified Reviews</div>
              </div>
            </div>

            {/* HQ Trust Card */}
            <div className="about-hq-card">
              <div className="hq-card-header">
                <span style={{ fontSize: '32px' }}>📍</span>
                <div>
                  <div className="hq-card-title">ElectroFix Lucknow Operations Center</div>
                  <div className="hq-card-address">TCG-2/2, Vibhuti Khand, Gomti Nagar, Lucknow, UP 226010</div>
                </div>
              </div>
              <div className="hq-pills-row">
                <span className="hq-pill">🛡️ ISO 9001 Protocol</span>
                <span className="hq-pill">⚡ 24/7 Rapid SOS Fleet</span>
                <span className="hq-pill">🤖 Ollama AI Powered</span>
                <span className="hq-pill">🔒 ₹10,000 Damage Cover</span>
              </div>
              <div className="hq-action-row">
                <button
                  type="button"
                  className="btn-card-diagnose"
                  style={{ flex: 1, padding: '11px' }}
                  onClick={() => setIsRobotForceOpen(true)}
                >
                  🤖 Ask Bijli Guru
                </button>
                <button
                  type="button"
                  className="btn-card-book"
                  style={{ flex: 1, padding: '11px' }}
                  onClick={() => openBookingModal(null, 'ElectroFix General Consultation')}
                >
                  ⚡ Book Inspection
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* 4.5 Interactive Electrical Risk Self-Assessment */}
      <section id="risk-checker" className="section-container" style={{ background: '#f8fafc' }}>
        <div className="section-header-centered">
          <span className="section-tag">Home Safety Health-Meter</span>
          <h2 className="section-title">Is Your Home at Risk of Electrical Fire or Shock?</h2>
          <p className="section-subtitle">
            Answer 3 quick questions about your home’s wiring symptoms. Get an instant risk calculation and personalized safety recommendations.
          </p>
        </div>

        <div className="risk-checker-wrapper">
          <div className="risk-questions-list">
            {/* Question 1 */}
            <div className="risk-question-group">
              <div className="risk-question-title">
                <span>🔥</span> 1. Plugs or switches sparking / burning plastic smell?
              </div>
              <div className="risk-options-row">
                <button
                  type="button"
                  className={`risk-option-btn ${riskAnswers.sparking === 0 ? 'active' : ''}`}
                  onClick={() => setRiskAnswers((p) => ({ ...p, sparking: 0 }))}
                >
                  🟢 Never (Clean)
                </button>
                <button
                  type="button"
                  className={`risk-option-btn ${riskAnswers.sparking === 1 ? 'active' : ''}`}
                  onClick={() => setRiskAnswers((p) => ({ ...p, sparking: 1 }))}
                >
                  🟡 Occasionally
                </button>
                <button
                  type="button"
                  className={`risk-option-btn ${riskAnswers.sparking === 2 ? 'active' : ''}`}
                  onClick={() => setRiskAnswers((p) => ({ ...p, sparking: 2 }))}
                >
                  🔴 Yes, Frequently
                </button>
              </div>
            </div>

            {/* Question 2 */}
            <div className="risk-question-group">
              <div className="risk-question-title">
                <span>⚡</span> 2. Tingling shock touching fridge, taps, or washing machine?
              </div>
              <div className="risk-options-row">
                <button
                  type="button"
                  className={`risk-option-btn ${riskAnswers.shocks === 0 ? 'active' : ''}`}
                  onClick={() => setRiskAnswers((p) => ({ ...p, shocks: 0 }))}
                >
                  🟢 Never
                </button>
                <button
                  type="button"
                  className={`risk-option-btn ${riskAnswers.shocks === 1 ? 'active' : ''}`}
                  onClick={() => setRiskAnswers((p) => ({ ...p, shocks: 1 }))}
                >
                  🟡 Mild Tingling
                </button>
                <button
                  type="button"
                  className={`risk-option-btn ${riskAnswers.shocks === 2 ? 'active' : ''}`}
                  onClick={() => setRiskAnswers((p) => ({ ...p, shocks: 2 }))}
                >
                  🔴 Sharp Shock Felt
                </button>
              </div>
            </div>

            {/* Question 3 */}
            <div className="risk-question-group">
              <div className="risk-question-title">
                <span>🔌</span> 3. MCB tripping when AC, geyser or microwave turns on?
              </div>
              <div className="risk-options-row">
                <button
                  type="button"
                  className={`risk-option-btn ${riskAnswers.tripping === 0 ? 'active' : ''}`}
                  onClick={() => setRiskAnswers((p) => ({ ...p, tripping: 0 }))}
                >
                  🟢 Stable (Rare)
                </button>
                <button
                  type="button"
                  className={`risk-option-btn ${riskAnswers.tripping === 1 ? 'active' : ''}`}
                  onClick={() => setRiskAnswers((p) => ({ ...p, tripping: 1 }))}
                >
                  🟡 Trips Sometimes
                </button>
                <button
                  type="button"
                  className={`risk-option-btn ${riskAnswers.tripping === 2 ? 'active' : ''}`}
                  onClick={() => setRiskAnswers((p) => ({ ...p, tripping: 2 }))}
                >
                  🔴 Trips Every Day
                </button>
              </div>
            </div>
          </div>

          {/* Result Card */}
          <div className={`risk-result-card ${riskLevel}`}>
            <span className="risk-meter-badge">
              {riskLevel === 'safe' ? '🟢 Safe Circuit Health' : riskLevel === 'moderate' ? '⚠️ Caution Advised' : '🚨 High Hazard Detected'}
            </span>

            <div className="risk-score-display">
              {riskLevel === 'safe' ? 'Low Risk' : riskLevel === 'moderate' ? 'Moderate' : 'Critical!'}
            </div>

            <p className="risk-advice-text">
              {riskLevel === 'safe' &&
                'Your home wiring appears stable. Annual maintenance and regular fan lubrication are all that is recommended to preserve line life.'}
              {riskLevel === 'moderate' &&
                'Symptoms indicate loose brass terminals or degraded earthing continuity. Schedule a checkup before summer heat or monsoons.'}
              {riskLevel === 'critical' &&
                'Severe electrical danger detected! Risk of wire insulation melting, appliance damage, or electric shock. Main power isolation and master technician inspection advised immediately.'}
            </p>

            <div className="risk-actions-col">
              <button
                type="button"
                className="btn-card-diagnose"
                style={{ width: '100%', padding: '12px', justifyContent: 'center' }}
                onClick={() =>
                  triggerAiDiagnostic(
                    `My home risk score is ${riskLevel.toUpperCase()}: Plugs sparking (${riskAnswers.sparking}), Tingling shock (${riskAnswers.shocks}), MCB tripping (${riskAnswers.tripping}). What should I do?`
                  )
                }
              >
                🤖 Ask Bijli Guru (Ollama)
              </button>

              <button
                type="button"
                className="btn-card-book"
                style={{ width: '100%', padding: '12px' }}
                onClick={() => openBookingModal(null, `Home Electrical Safety Audit (${riskLevel.toUpperCase()} Risk)`)}
              >
                ⚡ Book Inspection Audit (₹399)
              </button>
            </div>
          </div>
        </div>
      </section>

      {/* 5. Services & Transparent Rate Card */}
      <section id="services" className="section-container">
        <div className="section-header-centered">
          <span className="section-tag">Catalog & Rate Card</span>
          <h2 className="section-title">Transparent Electrical Services & Fixed Pricing</h2>
          <p className="section-subtitle">
            Zero hidden surcharges. All services include standard testing equipment, genuine ISI-spec safety
            materials, and a 30-day rework warranty.
          </p>
        </div>

        {/* Filter Tabs */}
        <div className="services-filter-tabs">
          <button
            type="button"
            className={`service-filter-btn ${serviceCategory === 'all' ? 'active' : ''}`}
            onClick={() => setServiceCategory('all')}
          >
            All Services ({SERVICES_CATALOG.length})
          </button>
          <button
            type="button"
            className={`service-filter-btn ${serviceCategory === 'fans' ? 'active' : ''}`}
            onClick={() => setServiceCategory('fans')}
          >
            🌀 Fans & Regulators
          </button>
          <button
            type="button"
            className={`service-filter-btn ${serviceCategory === 'switches' ? 'active' : ''}`}
            onClick={() => setServiceCategory('switches')}
          >
            🔌 Switches & Sockets
          </button>
          <button
            type="button"
            className={`service-filter-btn ${serviceCategory === 'breakers' ? 'active' : ''}`}
            onClick={() => setServiceCategory('breakers')}
          >
            ⚡ MCB & Tripping
          </button>
          <button
            type="button"
            className={`service-filter-btn ${serviceCategory === 'inverter' ? 'active' : ''}`}
            onClick={() => setServiceCategory('inverter')}
          >
            🔋 Inverter & Battery
          </button>
          <button
            type="button"
            className={`service-filter-btn ${serviceCategory === 'wiring' ? 'active' : ''}`}
            onClick={() => setServiceCategory('wiring')}
          >
            🛡️ Wiring & Earthing
          </button>
        </div>

        {/* Service Cards Grid */}
        <div className="services-cards-grid">
          {filteredServices.map((srv) => (
            <div key={srv.id} className="service-card">
              <div>
                <div className="service-card-top">
                  <div className="service-icon-box">{srv.icon}</div>
                  <span className="service-price-pill">{srv.price}</span>
                </div>
                <h3 className="service-card-title">{srv.title}</h3>
                <p className="service-card-desc">{srv.desc}</p>
                <ul className="service-features-list">
                  {srv.features.map((feat, fIdx) => (
                    <li key={fIdx}>{feat}</li>
                  ))}
                </ul>
              </div>

              <div className="service-card-actions">
                <button
                  type="button"
                  className="btn-card-book"
                  onClick={() => openBookingModal(null, srv.title)}
                >
                  Book ({srv.price})
                </button>
                <button
                  type="button"
                  className="btn-card-diagnose"
                  onClick={() => triggerAiDiagnostic(`I have an issue with ${srv.title}`)}
                  title="Diagnose with Bijli Guru"
                >
                  🤖 Diagnose
                </button>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* 5.1 Why Us / Mistri Comparison Matrix */}
      <section id="why-us" className="section-container why-us-section" style={{ background: '#f8fafc' }}>
        <div className="section-header-centered">
          <span className="section-tag">Comparison Matrix</span>
          <h2 className="section-title">Why Lucknow Chooses ElectroFix Over Local Mistris</h2>
          <p className="section-subtitle">
            See the honest difference between unverified roadside mechanics and ElectroFix certified master technicians.
          </p>
        </div>

        {/* Why Us Filter Tabs */}
        <div className="why-us-filter-tabs">
          <button
            type="button"
            className={`why-filter-btn ${whyUsTab === 'all' ? 'active' : ''}`}
            onClick={() => setWhyUsTab('all')}
          >
            All Comparison Factors ({WHY_US_COMPARISON_DATA.length})
          </button>
          <button
            type="button"
            className={`why-filter-btn ${whyUsTab === 'speed' ? 'active' : ''}`}
            onClick={() => setWhyUsTab('speed')}
          >
            ⏱️ Speed & Punctuality
          </button>
          <button
            type="button"
            className={`why-filter-btn ${whyUsTab === 'pricing' ? 'active' : ''}`}
            onClick={() => setWhyUsTab('pricing')}
          >
            💰 Pricing & Warranty
          </button>
          <button
            type="button"
            className={`why-filter-btn ${whyUsTab === 'safety' ? 'active' : ''}`}
            onClick={() => setWhyUsTab('safety')}
          >
            🛡️ Safety & Genuine Parts
          </button>
        </div>

        {/* Comparison Table / Grid */}
        <div className="comparison-table-wrapper">
          <div className="comparison-header-row">
            <div className="comp-col-feature">Service Standard</div>
            <div className="comp-col-mistri">❌ Traditional Roadside Mistri</div>
            <div className="comp-col-electrofix">✅ ElectroFix Certified Master</div>
          </div>

          {WHY_US_COMPARISON_DATA
            .filter((item) => whyUsTab === 'all' || item.category === whyUsTab)
            .map((item, idx) => (
              <div key={idx} className="comparison-item-row">
                <div className="comp-col-feature">
                  <span className="comp-row-icon">{item.icon}</span>
                  <strong>{item.feature}</strong>
                </div>
                <div className="comp-col-mistri">
                  <span className="comp-cross-badge">✕</span>
                  <span>{item.localMistri}</span>
                </div>
                <div className="comp-col-electrofix">
                  <span className="comp-check-badge">✓</span>
                  <span>{item.electroFix}</span>
                </div>
              </div>
            ))}
        </div>

        {/* Bottom Callout Bar */}
        <div className="comparison-cta-strip">
          <div>
            <strong>Ready to experience the certified standard in Lucknow?</strong>
            <p style={{ margin: 0, fontSize: '13px', color: '#64748b' }}>
              Book in 30 seconds with 100% upfront pricing and our 30-day rework warranty.
            </p>
          </div>
          <button
            type="button"
            className="btn-book-cta"
            onClick={() => openBookingModal(null, 'ElectroFix Verified Master Service')}
          >
            ⚡ Book a Verified Pro
          </button>
        </div>
      </section>

      {/* 5.2 Seasonal Electrical Safety Checklist */}
      <section id="safety-checklist" className="section-container safety-checklist-section">
        <div className="section-header-centered">
          <span className="section-tag">Seasonal Protection</span>
          <h2 className="section-title">Lucknow Seasonal Electrical Safety Checklist</h2>
          <p className="section-subtitle">
            Lucknow's extreme weather shifts — scorching 45°C summers, humid monsoons, and chilly winters — severely stress home wiring. Run your seasonal audit below!
          </p>
        </div>

        {/* Season Selector Tabs */}
        <div className="seasonal-tabs-row">
          {Object.entries(SEASONAL_CHECKLIST_DATA).map(([key, seasonObj]) => (
            <button
              key={key}
              type="button"
              className={`season-tab-btn ${seasonalTab === key ? 'active' : ''}`}
              onClick={() => setSeasonalTab(key)}
            >
              <span className="season-tab-icon">{seasonObj.icon}</span>
              <div className="season-tab-text">
                <span className="season-tab-title">{seasonObj.title.split(' ')[0]}</span>
                <span className="season-tab-sub">{seasonObj.season.split('(')[0]}</span>
              </div>
            </button>
          ))}
        </div>

        {/* Active Season Checklist Card */}
        {(() => {
          const season = SEASONAL_CHECKLIST_DATA[seasonalTab];
          return (
            <div className="seasonal-card-container">
              <div className="seasonal-card-left">
                <div className="season-banner-head">
                  <span className="season-badge">{season.season}</span>
                  <h3 className="season-name">{season.icon} {season.title}</h3>
                  <p className="season-summary">{season.summary}</p>
                </div>

                <div className="checklist-items-group">
                  <div className="checklist-instructions">
                    💡 <em>Click checkboxes to mark items inspected in your home:</em>
                  </div>

                  {season.items.map((item) => {
                    const isChecked = !!safetyChecklist[item.id];
                    return (
                      <div
                        key={item.id}
                        className={`checklist-item-card ${isChecked ? 'checked' : 'unchecked'}`}
                        onClick={() => toggleChecklistItem(item.id)}
                      >
                        <div className="custom-checkbox">
                          {isChecked ? '✓' : ''}
                        </div>
                        <div className="checklist-label-col">
                          <span className="checklist-item-text">{item.label}</span>
                          <span className={`checklist-risk-pill ${isChecked ? 'safe' : 'hazard'}`}>
                            {isChecked ? '🛡️ Protected' : `⚠️ ${item.risk}`}
                          </span>
                        </div>
                      </div>
                    );
                  })}
                </div>
              </div>

              {/* Score & Action Panel */}
              <div className="seasonal-card-right">
                <div className="checklist-score-dial">
                  <div className="dial-num">{seasonalScorePct}%</div>
                  <div className="dial-label">Home Safety Score</div>
                </div>

                <div className={`score-status-badge ${seasonalScorePct >= 75 ? 'status-green' : seasonalScorePct >= 50 ? 'status-amber' : 'status-red'}`}>
                  {seasonalScorePct >= 75
                    ? '🟢 Excellent Protection'
                    : seasonalScorePct >= 50
                    ? '🟡 Moderate Risk Detected'
                    : '🔴 Urgent Safety Audit Advised'}
                </div>

                <p className="score-summary-text">
                  {seasonalScorePct >= 75
                    ? 'Your electrical points for this season are in great shape. Keep up regular maintenance.'
                    : seasonalScorePct >= 50
                    ? 'Several critical seasonal safeguards are missing. Loose terminals or phase imbalance could trigger MCB tripping.'
                    : 'High risk of electrical hazard or appliance breakdown! We recommend a certified inspection before heavy usage.'}
                </p>

                <div className="score-actions-col">
                  <button
                    type="button"
                    className="btn-card-diagnose"
                    style={{ width: '100%', padding: '11px', justifyContent: 'center' }}
                    onClick={() =>
                      triggerAiDiagnostic(
                        `My ${season.title} electrical safety score is ${seasonalScorePct}%. Missing items: ${season.items
                          .filter((it) => !safetyChecklist[it.id])
                          .map((it) => it.label)
                          .join(', ')}. What should I do?`
                      )
                    }
                  >
                    🤖 Ask Bijli Guru to Analyze
                  </button>
                  <button
                    type="button"
                    className="btn-card-book"
                    style={{ width: '100%', padding: '11px' }}
                    onClick={() => openBookingModal(null, `Seasonal Inspection: ${season.title} (Score: ${seasonalScorePct}%)`)}
                  >
                    ⚡ Book Seasonal Audit (₹399)
                  </button>
                </div>
              </div>
            </div>
          );
        })()}
      </section>

      {/* 5.3 Our 6 Golden Guarantees */}
      <section id="guarantees" className="section-container guarantees-section" style={{ background: '#f8fafc' }}>
        <div className="section-header-centered">
          <span className="section-tag">Total Assurance</span>
          <h2 className="section-title">The ElectroFix 6 Golden Guarantees</h2>
          <p className="section-subtitle">
            We don't just fix electrical faults — we stand behind every single job with concrete guarantees for your family and home.
          </p>
        </div>

        <div className="guarantees-cards-grid">
          {GOLDEN_GUARANTEES_DATA.map((g, gIdx) => (
            <div key={gIdx} className="guarantee-card">
              <div className="guarantee-card-top">
                <span className="guarantee-icon">{g.icon}</span>
                <span className="guarantee-badge">{g.badge}</span>
              </div>
              <h3 className="guarantee-title">{g.title}</h3>
              <div className="guarantee-sub">{g.subtitle}</div>
              <p className="guarantee-desc">{g.desc}</p>
            </div>
          ))}
        </div>
      </section>

      {/* 5.5 Safety Audit & Annual AMC Packages */}
      <section id="packages" className="section-container" style={{ background: '#ffffff' }}>
        <div className="section-header-centered">
          <span className="section-tag">Preventive AMC Plans</span>
          <h2 className="section-title">Home Electrical Health & Annual Protection Plans</h2>
          <p className="section-subtitle">
            Prevent costly appliance burnout and hazardous short circuits before they happen. Fixed packages with genuine ISI parts and complete warranty coverage.
          </p>
        </div>

        <div className="packages-cards-grid">
          {PACKAGES_DATA.map((pkg) => (
            <div key={pkg.id} className={`package-card ${pkg.popular ? 'popular' : ''}`}>
              {pkg.popular && <div className="package-ribbon-tag">{pkg.badge}</div>}

              <div>
                <div className="package-header">
                  <div className="package-icon-box">{pkg.icon}</div>
                  <h3 className="package-title">{pkg.title}</h3>
                  <p className="package-period-desc">{pkg.period}</p>
                </div>

                <div className="package-price-wrap">
                  <span className="package-price-num">{pkg.price}</span>
                  <span className="package-price-cycle">/ package</span>
                </div>

                <p style={{ fontSize: '13px', color: '#64748b', marginBottom: '20px', lineHeight: '1.5' }}>
                  {pkg.desc}
                </p>

                <ul className="package-features-list">
                  {pkg.features.map((feat, fIdx) => (
                    <li key={fIdx} className="package-feature-item">
                      <span className="check-icon">✓</span>
                      <span>{feat}</span>
                    </li>
                  ))}
                </ul>
              </div>

              <button
                type="button"
                className="btn-package-select"
                onClick={() => openBookingModal(null, `${pkg.title} (${pkg.price})`)}
              >
                Book {pkg.title.split(' ')[0]} ({pkg.price})
              </button>
            </div>
          ))}
        </div>
      </section>

      {/* 6. Interactive Rate Calculator */}
      <section id="calculator" className="section-container">
        <div className="section-header-centered">
          <span className="section-tag">Interactive Cost Estimator</span>
          <h2 className="section-title">Instant Repair Cost Calculator</h2>
          <p className="section-subtitle">
            Calculate your estimated service bill upfront. Select the appliances or repairs you need,
            see transparent itemized prices, and book in 1 click!
          </p>
        </div>

        <div className="calculator-wrapper">
          {/* Selectors Column */}
          <div className="calc-selectors-col">
            {/* Ceiling Fans Stepper */}
            <div>
              <div className="calc-group-label">Ceiling / Exhaust Fan Repairs (₹149 each)</div>
              <div className="calc-stepper-row">
                <span style={{ fontSize: '14px', fontWeight: '700' }}>🌀 Fans to Inspect / Fix</span>
                <div className="stepper-controls">
                  <button
                    type="button"
                    className="stepper-btn"
                    onClick={() => setCalcItems((p) => ({ ...p, fans: Math.max(0, p.fans - 1) }))}
                  >
                    -
                  </button>
                  <span className="stepper-value">{calcItems.fans}</span>
                  <button
                    type="button"
                    className="stepper-btn"
                    onClick={() => setCalcItems((p) => ({ ...p, fans: p.fans + 1 }))}
                  >
                    +
                  </button>
                </div>
              </div>
            </div>

            {/* Switches & Sockets Stepper */}
            <div>
              <div className="calc-group-label">Switches & Modular Sockets (₹99 each)</div>
              <div className="calc-stepper-row">
                <span style={{ fontSize: '14px', fontWeight: '700' }}>🔌 Switch / Socket Units</span>
                <div className="stepper-controls">
                  <button
                    type="button"
                    className="stepper-btn"
                    onClick={() => setCalcItems((p) => ({ ...p, switches: Math.max(0, p.switches - 1) }))}
                  >
                    -
                  </button>
                  <span className="stepper-value">{calcItems.switches}</span>
                  <button
                    type="button"
                    className="stepper-btn"
                    onClick={() => setCalcItems((p) => ({ ...p, switches: p.switches + 1 }))}
                  >
                    +
                  </button>
                </div>
              </div>
            </div>

            {/* Toggle Options */}
            <div>
              <div className="calc-group-label">Specialist Diagnostic & Heavy Services</div>
              <div className="calc-options-grid">
                <button
                  type="button"
                  className={`calc-option-btn ${calcItems.mcb > 0 ? 'selected' : ''}`}
                  onClick={() => setCalcItems((p) => ({ ...p, mcb: p.mcb > 0 ? 0 : 1 }))}
                >
                  <span className="calc-option-title">⚡ MCB Tripping Test</span>
                  <span className="calc-option-rate">+ ₹199</span>
                </button>

                <button
                  type="button"
                  className={`calc-option-btn ${calcItems.inverter > 0 ? 'selected' : ''}`}
                  onClick={() => setCalcItems((p) => ({ ...p, inverter: p.inverter > 0 ? 0 : 1 }))}
                >
                  <span className="calc-option-title">🔋 Inverter / Battery</span>
                  <span className="calc-option-rate">+ ₹249</span>
                </button>

                <button
                  type="button"
                  className={`calc-option-btn ${calcItems.wiring > 0 ? 'selected' : ''}`}
                  onClick={() => setCalcItems((p) => ({ ...p, wiring: p.wiring > 0 ? 0 : 1 }))}
                >
                  <span className="calc-option-title">🛡️ Full House Earthing</span>
                  <span className="calc-option-rate">+ ₹499</span>
                </button>

                <button
                  type="button"
                  className={`calc-option-btn ${calcItems.geyser > 0 ? 'selected' : ''}`}
                  onClick={() => setCalcItems((p) => ({ ...p, geyser: p.geyser > 0 ? 0 : 1 }))}
                >
                  <span className="calc-option-title">🚿 Heavy 16A Power Plug</span>
                  <span className="calc-option-rate">+ ₹199</span>
                </button>
              </div>
            </div>
          </div>

          {/* Receipt Column */}
          <div className="calc-receipt-card">
            <h3 className="receipt-title">
              <span>🧾</span> Estimated Service Invoice
            </h3>

            <div className="receipt-items-list">
              {calcItems.fans > 0 && (
                <div className="receipt-row">
                  <span>Fan Service ({calcItems.fans}x)</span>
                  <span>₹{calcItems.fans * 149}</span>
                </div>
              )}
              {calcItems.switches > 0 && (
                <div className="receipt-row">
                  <span>Switch / Socket ({calcItems.switches}x)</span>
                  <span>₹{calcItems.switches * 99}</span>
                </div>
              )}
              {calcItems.mcb > 0 && (
                <div className="receipt-row">
                  <span>MCB Tripping Diagnostic</span>
                  <span>₹199</span>
                </div>
              )}
              {calcItems.inverter > 0 && (
                <div className="receipt-row">
                  <span>Inverter & Battery Setup</span>
                  <span>₹249</span>
                </div>
              )}
              {calcItems.wiring > 0 && (
                <div className="receipt-row">
                  <span>Full Earthing Safety Audit</span>
                  <span>₹499</span>
                </div>
              )}
              {calcItems.geyser > 0 && (
                <div className="receipt-row">
                  <span>Heavy 16A Power Wiring</span>
                  <span>₹199</span>
                </div>
              )}

              <div className="receipt-row">
                <span>Inspection & Travel Surcharge</span>
                <span style={{ color: '#34d399', fontWeight: '700' }}>₹0 (FREE)</span>
              </div>
            </div>

            <div className="receipt-total-row">
              <span>Total Estimated:</span>
              <span className="receipt-total-price">₹{calcSubtotal || 99}</span>
            </div>

            <div className="receipt-warranty-badge">
              🛡️ 30-Day Free Re-Service Warranty Included
            </div>

            <button
              type="button"
              className="btn-book-from-calc"
              onClick={() => openBookingModal(null, `Calculated Services (Est: ₹${calcSubtotal})`)}
            >
              Book at this Estimate (₹{calcSubtotal || 99})
            </button>
          </div>
        </div>
      </section>

      {/* 6.5 Interactive Appliance Energy & Tariff Calculator */}
      <section id="energy-guide" className="section-container" style={{ background: '#f8fafc' }}>
        <div className="section-header-centered">
          <span className="section-tag">Lucknow Electricity Tariff Guide</span>
          <h2 className="section-title">Smart Appliance Power & Bill Estimator</h2>
          <p className="section-subtitle">
            Curious how much power your AC, geyser, and fans consume? Slide your daily usage hours below to see live kilowatt-hour (unit) estimates based on Lucknow domestic tariff slabs (~₹6.50/unit).
          </p>
        </div>

        <div className="energy-guide-card">
          <div className="energy-inputs-col">
            {/* AC Slider */}
            <div className="energy-slider-group">
              <div className="energy-slider-top">
                <span>❄️ 1.5 Ton AC Daily Runtime:</span>
                <span className="energy-slider-val">{energyAcHours} Hours / day</span>
              </div>
              <input
                type="range"
                min="0"
                max="16"
                step="1"
                value={energyAcHours}
                onChange={(e) => setEnergyAcHours(Number(e.target.value))}
                className="energy-range-input"
              />
              <span style={{ fontSize: '11.5px', color: '#64748b' }}>
                Consumes ~1.5 units/hr. Running {energyAcHours}h/day uses ~{energyAcHours * 45} units/mo (~₹{Math.round(energyAcHours * 45 * 6.5)}).
              </span>
            </div>

            {/* Geyser Slider */}
            <div className="energy-slider-group">
              <div className="energy-slider-top">
                <span>🚿 Water Geyser Daily Runtime:</span>
                <span className="energy-slider-val">{energyGeyserMins} Mins / day</span>
              </div>
              <input
                type="range"
                min="0"
                max="120"
                step="15"
                value={energyGeyserMins}
                onChange={(e) => setEnergyGeyserMins(Number(e.target.value))}
                className="energy-range-input"
              />
              <span style={{ fontSize: '11.5px', color: '#64748b' }}>
                2,000W heating coil consumes ~1 unit every 30 minutes (~₹6.50).
              </span>
            </div>

            {/* Fans Stepper */}
            <div className="energy-slider-group">
              <div className="energy-slider-top">
                <span>🌀 Active Ceiling Fans in Home:</span>
                <span className="energy-slider-val">{energyFansCount} Fans (12h avg)</span>
              </div>
              <input
                type="range"
                min="1"
                max="8"
                step="1"
                value={energyFansCount}
                onChange={(e) => setEnergyFansCount(Number(e.target.value))}
                className="energy-range-input"
              />
            </div>

            {/* BLDC Fan Upgrade Switch */}
            <div className="energy-bldc-toggle-card">
              <div>
                <div style={{ fontSize: '13.5px', fontWeight: '800', color: '#0f172a' }}>
                  Upgrade to Energy-Saving BLDC Fans (28W vs 75W)?
                </div>
                <div style={{ fontSize: '12px', color: '#64748b' }}>
                  BLDC brushless DC motor saves up to 63% power per fan.
                </div>
              </div>
              <button
                type="button"
                className={`service-filter-btn ${energyBldcToggle ? 'active' : ''}`}
                style={{ padding: '6px 14px', fontSize: '12px', whiteSpace: 'nowrap' }}
                onClick={() => setEnergyBldcToggle(!energyBldcToggle)}
              >
                {energyBldcToggle ? '✅ BLDC Enabled' : '○ Enable BLDC'}
              </button>
            </div>
          </div>

          {/* Energy Bill Preview */}
          <div className="energy-preview-card">
            <div>
              <div className="energy-tariff-tag">⚡ UPPCL Standard Domestic Rate: ~₹6.50 / kWh</div>
              <div style={{ fontSize: '13px', color: '#94a3b8', marginTop: '14px' }}>
                Estimated Monthly Major Load
              </div>
              <div className="energy-bill-figures">
                <div className="energy-bill-amount">₹{estimatedBillMonthly.toLocaleString()}</div>
                <div className="energy-units-text">
                  Approx. <strong>{totalUnitsMonthly} Units (kWh)</strong> / month
                </div>
              </div>
            </div>

            {energyBldcToggle ? (
              <div className="energy-savings-chip">
                🎉 <strong>Saving ~₹{bldcSavingsMonthly}/mo</strong> with BLDC fans! That's over ₹{(bldcSavingsMonthly * 12).toLocaleString()}/year saved on your bill.
              </div>
            ) : (
              <div style={{ background: 'rgba(255,255,255,0.06)', borderRadius: '8px', padding: '12px', fontSize: '12.5px', color: '#cbd5e1' }}>
                💡 <em>Pro-Tip: Keeping your AC at 24°C instead of 18°C saves an extra 36% electricity automatically.</em>
              </div>
            )}

            <button
              type="button"
              className="btn-book-cta"
              style={{ width: '100%', justifyContent: 'center' }}
              onClick={() => triggerAiDiagnostic('Ghar ka bijli bill kaise kam karein? BLDC fan aur AC setting ke baare mein batao')}
            >
              🤖 Ask Bijli Guru for Energy Savings
            </button>
          </div>
        </div>
      </section>

      {/* 7. Live Interactive Map & Nearby Technicians */}
      <section id="technicians" className="map-section-wrapper">
        <div className="map-section-inner">
          <div className="map-control-header">
            <div>
              <span className="section-tag">Live GPS Explorer</span>
              <h2 className="section-title" style={{ margin: '4px 0 6px' }}>
                Electricians Near Where You Live
              </h2>
              <p style={{ margin: 0, color: '#64748b', fontSize: '14px' }}>
                Sorted by real-time distance from your home (<strong>{customerLocation.areaName}</strong>).
                Drag the <strong>"🏠 You Live Here"</strong> pin to your exact building!
              </p>
            </div>

            <div className="map-control-actions">
              <button
                type="button"
                className="btn-detect-gps"
                onClick={detectLocationFromBrowser}
                title="Detect GPS from device"
              >
                <span>📍</span> Detect My Location
              </button>
              <div className="map-active-locality-pill">
                🏠 {customerLocation.areaName}
              </div>
            </div>
          </div>

          {/* Quick Locality Tabs */}
          <div className="locality-scroll-tabs">
            <span style={{ fontSize: '13px', fontWeight: '700', color: '#64748b', whiteSpace: 'nowrap' }}>
              Quick Zone:
            </span>
            {LUCKNOW_AREAS.map((area) => (
              <button
                key={area.id}
                type="button"
                className={`locality-tab-btn ${selectedArea === area.id ? 'active' : ''}`}
                onClick={() => handleAreaChange(area.id)}
              >
                {area.name} (~{area.eta})
              </button>
            ))}
          </div>

          {/* Map Grid */}
          <div className="map-technicians-grid">
            {/* Left Leaflet Frame */}
            <div>
              <div ref={mapContainerRef} className="leaflet-map-frame" />
              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '10px', fontSize: '12px', color: '#64748b', fontWeight: '600', flexWrap: 'wrap', gap: '8px' }}>
                <div style={{ display: 'flex', gap: '14px' }}>
                  <span style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                    <span style={{ fontSize: '14px' }}>🏠</span>
                    <strong style={{ color: '#2563eb' }}>You Live Here</strong>
                  </span>
                  <span style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                    <span style={{ width: '12px', height: '12px', borderRadius: '50%', background: '#f59e0b', display: 'inline-block' }}></span>
                    ⚡ Verified Electrician
                  </span>
                </div>
                <span style={{ color: '#475569', fontStyle: 'italic', fontSize: '11px' }}>
                  💡 Click map or drag pin to update your doorstep location!
                </span>
              </div>
            </div>

            {/* Right Technicians Column */}
            <div className="technicians-cards-scroll">
              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <h3 style={{ fontSize: '16px', fontWeight: '800', color: '#0f172a', margin: 0 }}>
                  Active Technicians Near You ({electricians.length})
                </h3>
                {loading && <span style={{ fontSize: '12px', color: '#2563eb', fontWeight: '700' }}>Locating...</span>}
              </div>

              {electricians.length === 0 ? (
                <div style={{ background: '#ffffff', border: '1px dashed #cbd5e1', borderRadius: '12px', padding: '36px 20px', textAlign: 'center', color: '#64748b' }}>
                  <div style={{ fontSize: '32px', marginBottom: '8px' }}>⚡</div>
                  <h4 style={{ fontSize: '16px', fontWeight: '700', color: '#0f172a' }}>
                    Searching for nearby pros...
                  </h4>
                </div>
              ) : (
                electricians.map((elec) => {
                  const isSelected = selectedElectrician?.id === elec.id;
                  return (
                    <div
                      key={elec.id}
                      className={`technician-item-card ${isSelected ? 'selected' : ''}`}
                      onClick={() => handleSelectElectrician(elec)}
                    >
                      <div className="pro-header-row">
                        <div className="pro-avatar-name">
                          <div className="pro-avatar-circle">
                            {elec.name.charAt(0)}
                          </div>
                          <div>
                            <div className="pro-name-title">{elec.name}</div>
                            <div className="pro-spec-subtitle">
                              📍 {elec.area} • {elec.specialization || 'Master Electrician'}
                            </div>
                          </div>
                        </div>
                        <span className="pro-distance-badge">
                          {elec.distance} away
                        </span>
                      </div>

                      <div className="pro-meta-bar" style={{ flexWrap: 'wrap', gap: '6px' }}>
                        <span className="pro-rating-tag">⭐ {elec.rating || '4.85'} ({elec.completed_jobs || 120}+ jobs)</span>
                        <span className="pro-status-tag">⏱️ {elec.experience || '6+ Yrs'}</span>
                        <span style={{ color: '#7e22ce', fontWeight: '800' }}>{elec.starting_price || '₹149'}</span>
                        {elec.badge && <span style={{ fontSize: '11px', background: '#f1f5f9', color: '#475569', padding: '2px 6px', borderRadius: '4px', fontWeight: '600' }}>🛡️ {elec.badge}</span>}
                      </div>

                      <div className="pro-card-buttons">
                        <button
                          type="button"
                          className="btn-pro-book"
                          onClick={(e) => {
                            e.stopPropagation();
                            openBookingModal(elec, 'Doorstep Repair');
                          }}
                        >
                          Book {elec.name.split(' ')[0]}
                        </button>
                        <a
                          href={`tel:${elec.phone}`}
                          className="btn-pro-call"
                          onClick={(e) => e.stopPropagation()}
                        >
                          📞 Call
                        </a>
                      </div>
                    </div>
                  );
                })
              )}
            </div>
          </div>
        </div>
      </section>

      {/* 8. How It Works Section */}
      <section id="how-it-works" className="section-container">
        <div className="section-header-centered">
          <span className="section-tag">Frictionless Process</span>
          <h2 className="section-title">How ElectroFix Works in 4 Steps</h2>
          <p className="section-subtitle">
            From initial fault diagnosis to certified doorstep completion, we make electrical repairs
            simple, fast, and completely stress-free.
          </p>
        </div>

        <div className="how-it-works-grid">
          <div className="step-card">
            <div className="step-number-badge">1</div>
            <h3 className="step-title">Diagnose or Select</h3>
            <p className="step-desc">
              Choose your repair from the rate card or describe symptoms to Bijli Guru AI for instant diagnosis.
            </p>
          </div>

          <div className="step-card">
            <div className="step-number-badge">2</div>
            <h3 className="step-title">Closest Pro Matched</h3>
            <p className="step-desc">
              Our intelligent GPS dispatcher pairs you with the closest verified master technician in your Lucknow zone.
            </p>
          </div>

          <div className="step-card">
            <div className="step-number-badge">3</div>
            <h3 className="step-title">25-Min Doorstep Fix</h3>
            <p className="step-desc">
              Pro arrives equipped with standard digital meters, safety insulated tools, and genuine ISI-certified spares.
            </p>
          </div>

          <div className="step-card">
            <div className="step-number-badge">4</div>
            <h3 className="step-title">Test & 30-Day Warranty</h3>
            <p className="step-desc">
              Inspect the completed repair together, pay via UPI/Cash, and get your 30-day warranty card on your phone.
            </p>
          </div>
        </div>
      </section>

      {/* 8.5 Verified Master Technicians Showcase & Tools */}
      <section id="masters" className="section-container" style={{ background: '#f8fafc' }}>
        <div className="section-header-centered">
          <span className="section-tag">Lucknow Master Technicians</span>
          <h2 className="section-title">Meet Our Lead Certified Electricians</h2>
          <p className="section-subtitle">
            Every technician is police-verified, background checked, carries valid govt certification, and arrives with German-standard 1,000V insulated safety equipment.
          </p>
        </div>

        <div className="tech-showcase-grid">
          {TECHNICIANS_SHOWCASE_DATA.map((pro, pIdx) => (
            <div key={pIdx} className="tech-showcase-card">
              <div className="tech-card-avatar-row">
                <div className="tech-avatar-circle">{pro.avatarText}</div>
                <div>
                  <h3 className="tech-name">{pro.name}</h3>
                  <div className="tech-exp-text">📍 {pro.area}</div>
                  <span className="tech-badge-pill">🛡️ {pro.badge}</span>
                </div>
              </div>

              <div style={{ fontSize: '12.5px', color: '#475569', lineHeight: '1.4' }}>
                <strong>{pro.role}</strong> • {pro.exp}
              </div>

              <div style={{ fontSize: '12px', color: '#f59e0b', fontWeight: '700' }}>
                ⭐ {pro.rating} ({pro.jobs})
              </div>

              <div className="tech-skills-chips">
                {pro.skills.map((sk, sIdx) => (
                  <span key={sIdx} className="tech-skill-chip">{sk}</span>
                ))}
              </div>

              <button
                type="button"
                className="btn-card-book"
                style={{ marginTop: 'auto', padding: '9px 12px', fontSize: '13px' }}
                onClick={() => openBookingModal(null, `Doorstep Visit with ${pro.name}`)}
              >
                Book {pro.name.split(' ')[0]}
              </button>
            </div>
          ))}
        </div>

        {/* Equipment Standards Bar */}
        <div className="tech-tools-strip-box">
          <div style={{ fontSize: '14px', fontWeight: '800', color: '#0f172a' }}>
            🧰 Standard Inspection Toolkit Carried on Every Visit:
          </div>
          <div className="tech-tool-item">
            <span>🛡️</span> VDE 1,000V Insulated Safety Handtools
          </div>
          <div className="tech-tool-item">
            <span>⚡</span> True-RMS Digital Multimeters & Clamp
          </div>
          <div className="tech-tool-item">
            <span>🌍</span> Ground Earth Resistance Loop Tester
          </div>
          <div className="tech-tool-item">
            <span>🌡️</span> Non-Contact Thermal Infrared Breaker Sensor
          </div>
        </div>
      </section>

      {/* 9. Safety Standards & Guarantees */}
      <section id="safety" className="section-container">
        <div className="section-header-centered">
          <span className="section-tag">Safety Standards</span>
          <h2 className="section-title">Why 14,000+ Lucknow Families Trust Us</h2>
          <p className="section-subtitle">
            Electricity demands uncompromising safety. We protect your home, family, and expensive appliances
            with certified protocol.
          </p>
        </div>

        <div className="safety-features-grid">
          <div className="safety-card">
            <div className="safety-card-icon">🛡️</div>
            <h3 className="safety-card-title">Police-Checked Technicians</h3>
            <p className="safety-card-desc">
              Every electrician undergoes thorough identity verification, background screening, and local police verification.
            </p>
          </div>

          <div className="safety-card">
            <div className="safety-card-icon">⚡</div>
            <h3 className="safety-card-title">ISI Mark & Pure Copper</h3>
            <p className="safety-card-desc">
              We strictly reject counterfeit wires. All recommended replacements are ISI-certified with 100% electrolytic copper.
            </p>
          </div>

          <div className="safety-card">
            <div className="safety-card-icon">🔒</div>
            <h3 className="safety-card-title">₹10,000 Damage Cover</h3>
            <p className="safety-card-desc">
              Complete peace of mind. Every service is backed by our customer property damage insurance protection.
            </p>
          </div>

          <div className="safety-card">
            <div className="safety-card-icon">💸</div>
            <h3 className="safety-card-title">Zero Surge Pricing</h3>
            <p className="safety-card-desc">
              Fair, fixed prices. No hidden peak-hour fees, rain surcharges, or arbitrary doorstep bargaining.
            </p>
          </div>
        </div>
      </section>

      {/* 9.5 Commercial, RWA Societies & EV Charger Infrastructure */}
      <section id="commercial" className="section-container" style={{ background: '#f8fafc' }}>
        <div className="section-header-centered">
          <span className="section-tag">Enterprise & Community</span>
          <h2 className="section-title">Commercial Buildings, RWAs & Home EV Chargers</h2>
          <p className="section-subtitle">
            Reliable 3-phase commercial wiring, residential society electrical maintenance, and certified home EV wallbox charging setups across Lucknow.
          </p>
        </div>

        <div className="commercial-solutions-grid">
          {COMMERCIAL_SOLUTIONS_DATA.map((sol, cIdx) => (
            <div key={cIdx} className="commercial-solution-card">
              <div>
                <div className="commercial-icon-head">{sol.icon}</div>
                <h3 className="commercial-title">{sol.title}</h3>
                <p className="commercial-desc">{sol.desc}</p>
                <ul className="package-features-list" style={{ marginTop: '16px' }}>
                  {sol.features.map((feat, fIdx) => (
                    <li key={fIdx} className="package-feature-item">
                      <span className="check-icon">✓</span>
                      <span>{feat}</span>
                    </li>
                  ))}
                </ul>
              </div>

              <div style={{ display: 'flex', gap: '10px' }}>
                <button
                  type="button"
                  className="btn-card-book"
                  style={{ flex: 1, padding: '11px' }}
                  onClick={() => openBookingModal(null, sol.title)}
                >
                  Request Survey / Quote
                </button>
                <a
                  href="tel:+919812345678"
                  className="btn-pro-call"
                  style={{ padding: '11px 14px' }}
                >
                  📞 Call
                </a>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* 10. Lucknow Locality Coverage Grid */}
      <section id="coverage" className="section-container">
        <div className="section-header-centered">
          <span className="section-tag">Lucknow City Coverage</span>
          <h2 className="section-title">Rapid Service Across All 12 Lucknow Zones</h2>
          <p className="section-subtitle">
            Click any locality below to immediately center the live technician map and check arrival estimates!
          </p>
        </div>

        <div className="coverage-zones-grid">
          {LUCKNOW_AREAS.map((area) => (
            <div
              key={area.id}
              className="zone-badge-card"
              onClick={() => {
                handleAreaChange(area.id);
                document.getElementById('technicians')?.scrollIntoView({ behavior: 'smooth' });
              }}
            >
              <span className="zone-name">{area.name}</span>
              <span className="zone-eta">⏱️ ~{area.eta} arrival</span>
            </div>
          ))}
        </div>
      </section>

      {/* 10.5 Real Lucknow Case Studies (Before & After) */}
      <section id="case-studies" className="section-container" style={{ background: '#f8fafc' }}>
        <div className="section-header-centered">
          <span className="section-tag">Real Workmanship</span>
          <h2 className="section-title">Real Lucknow Case Studies & Problem Solving</h2>
          <p className="section-subtitle">
            See how our master technicians diagnosed and fixed severe electrical hazards in real Lucknow homes.
          </p>
        </div>

        <div className="case-studies-container">
          {/* Tabs Bar */}
          <div className="case-study-tabs-bar">
            {CASE_STUDIES_DATA.map((cs, csIdx) => (
              <button
                key={cs.id}
                type="button"
                className={`case-study-tab-btn ${activeCaseStudy === csIdx ? 'active' : ''}`}
                onClick={() => setActiveCaseStudy(csIdx)}
              >
                <span>{cs.icon}</span>
                <span>{cs.title}</span>
              </button>
            ))}
          </div>

          {/* Active Case Study Panel */}
          {(() => {
            const cs = CASE_STUDIES_DATA[activeCaseStudy];
            return (
              <div className="case-study-content-panel">
                <div>
                  <div className="case-study-tag-strip">
                    <span className="case-study-locality-tag">📍 {cs.locality}</span>
                    <span className="case-study-time-tag">⏱️ {cs.time}</span>
                    <span style={{ fontSize: '12px', fontWeight: '700', color: '#b91c1c' }}>
                      ⚠️ {cs.urgency}
                    </span>
                  </div>

                  <h3 style={{ fontSize: '22px', fontWeight: '800', color: '#0f172a', margin: '0 0 16px' }}>
                    {cs.title}
                  </h3>

                  <div className="case-study-prob-box">
                    <div style={{ fontSize: '12px', fontWeight: '800', color: '#b91c1c', marginBottom: '4px' }}>
                      🚨 THE PROBLEM IDENTIFIED:
                    </div>
                    <p style={{ margin: 0, fontSize: '13.5px', color: '#7f1d1d', lineHeight: '1.5' }}>
                      {cs.problem}
                    </p>
                  </div>

                  <div className="case-study-sol-box">
                    <div style={{ fontSize: '12px', fontWeight: '800', color: '#065f46', marginBottom: '4px' }}>
                      ✅ CERTIFIED MASTER REPAIR EXECUTED:
                    </div>
                    <p style={{ margin: 0, fontSize: '13.5px', color: '#064e3b', lineHeight: '1.5' }}>
                      {cs.solution}
                    </p>
                  </div>
                </div>

                {/* Right Result Card */}
                <div style={{ background: '#ffffff', border: '1.5px solid #cbd5e1', borderRadius: '16px', padding: '28px 24px', textAlign: 'center' }}>
                  <div style={{ fontSize: '42px', marginBottom: '12px' }}>{cs.icon}</div>
                  <h4 style={{ fontSize: '18px', fontWeight: '800', color: '#0f172a', margin: '0 0 8px' }}>
                    Verified Resolution
                  </h4>
                  <p style={{ fontSize: '13.5px', color: '#475569', lineHeight: '1.5', margin: '0 0 18px' }}>
                    {cs.result}
                  </p>
                  <div style={{ background: '#eff6ff', borderRadius: '8px', padding: '10px 14px', fontSize: '13px', fontWeight: '800', color: '#1e40af', marginBottom: '18px' }}>
                    📊 {cs.stats}
                  </div>
                  <button
                    type="button"
                    className="btn-card-book"
                    style={{ width: '100%', padding: '12px' }}
                    onClick={() => openBookingModal(null, `Similar Fix: ${cs.title}`)}
                  >
                    Book Similar Repair Service
                  </button>
                </div>
              </div>
            );
          })()}
        </div>
      </section>

      {/* 11. Customer Reviews */}
      <section id="reviews" className="section-container">
        <div className="section-header-centered">
          <span className="section-tag">Verified Homeowner Feedback</span>
          <h2 className="section-title">What Lucknow Residents Say</h2>
          <p className="section-subtitle">
            Real experiences from families across Gomti Nagar, Indira Nagar, Hazratganj, and Aliganj.
          </p>
        </div>

        <div className="reviews-cards-grid">
          {REVIEWS_DATA.map((rev, rIdx) => (
            <div key={rIdx} className="review-card">
              <div>
                <div className="review-stars-row">
                  {'★'.repeat(rev.rating)}
                </div>
                <p className="review-quote-text">"{rev.text}"</p>
              </div>

              <div className="reviewer-meta">
                <div className="reviewer-avatar">
                  {rev.name.charAt(0)}
                </div>
                <div>
                  <div className="reviewer-name">{rev.name}</div>
                  <div className="reviewer-locality">📍 {rev.area} • <em>{rev.service}</em></div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* 11.5 Lucknow Community Emergency & Helpline Directory */}
      <section id="helpline" className="section-container helpline-section" style={{ background: '#f8fafc' }}>
        <div className="section-header-centered">
          <span className="section-tag">Lucknow City Directory</span>
          <h2 className="section-title">Lucknow Electrical Emergency Helplines & Substations</h2>
          <p className="section-subtitle">
            Essential direct contact numbers for UPPCL power grid breakdowns, local Lucknow division substations, and ElectroFix 24/7 rapid emergency dispatch.
          </p>
        </div>

        {/* 4-Step Disaster Protocol Strip */}
        <div className="emergency-protocol-card">
          <div className="protocol-head">
            <span style={{ fontSize: '24px' }}>🚨</span>
            <div>
              <h3 style={{ fontSize: '17px', fontWeight: '800', color: '#b91c1c', margin: 0 }}>
                Immediate 4-Step Electrical Emergency Action Protocol
              </h3>
              <p style={{ margin: '4px 0 0', fontSize: '13px', color: '#7f1d1d' }}>
                Follow these critical safety steps before approaching any sparking fixture or flooded circuit:
              </p>
            </div>
          </div>
          <div className="protocol-steps-grid">
            <div className="protocol-step-box">
              <span className="protocol-step-badge">1</span>
              <strong>Isolate Main MCB</strong>
              <p>Trip main double-pole isolator switch immediately if safe to reach.</p>
            </div>
            <div className="protocol-step-box">
              <span className="protocol-step-badge">2</span>
              <strong>Zero Water Contact</strong>
              <p>Do NOT step in puddles or touch wet walls near sparking switches.</p>
            </div>
            <div className="protocol-step-box">
              <span className="protocol-step-badge">3</span>
              <strong>Keep Clear</strong>
              <p>Keep children and pets completely away from smoking or buzzing panels.</p>
            </div>
            <div className="protocol-step-box">
              <span className="protocol-step-badge">4</span>
              <strong>Dispatch SOS</strong>
              <p>Call our rapid response pro or dial UPPCL 1912 for feeder breakdown.</p>
            </div>
          </div>
        </div>

        {/* Helpline Cards Grid */}
        <div className="helpline-cards-grid">
          {LUCKNOW_HELPLINE_DATA.map((line, hIdx) => (
            <div key={hIdx} className="helpline-card">
              <div className="helpline-card-top">
                <span className="helpline-icon">{line.icon}</span>
                <span className="helpline-timing-pill">⏱️ {line.timing}</span>
              </div>
              <h3 className="helpline-title">{line.division}</h3>
              <div className="helpline-type-tag">{line.type}</div>
              <p className="helpline-desc">{line.desc}</p>
              <div className="helpline-action-row">
                <a href={`tel:${line.phone.replace(/[^0-9+]/g, '')}`} className="btn-helpline-call">
                  📞 Call {line.phone}
                </a>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* 12. Interactive FAQ Accordion */}
      <section id="faq" className="section-container">
        <div className="section-header-centered">
          <span className="section-tag">Got Questions?</span>
          <h2 className="section-title">Frequently Asked Questions</h2>
          <p className="section-subtitle">
            Everything you need to know about our electricians, Ollama AI diagnosis, guarantees, and pricing.
          </p>
        </div>

        <div className="faq-accordion-list">
          {FAQ_DATA.map((item, fIdx) => {
            const isOpen = activeFaq === fIdx;
            return (
              <div key={fIdx} className={`faq-item ${isOpen ? 'open' : ''}`}>
                <button
                  type="button"
                  className="faq-question-btn"
                  onClick={() => setActiveFaq(isOpen ? null : fIdx)}
                >
                  <span>{item.q}</span>
                  <span className="faq-toggle-icon">{isOpen ? '✕' : '+'}</span>
                </button>
                {isOpen && (
                  <div className="faq-answer-body">
                    {item.a}
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </section>

      {/* 13. Contact & AI Dispatch Desk Section (#contact) */}
      <section id="contact" className="section-container contact-section" style={{ background: '#f8fafc' }}>
        <div className="section-header-centered">
          <span className="section-tag">24/7 Dispatch Desk</span>
          <h2 className="section-title">Contact & AI Dispatch Support</h2>
          <p className="section-subtitle">
            Send an instant contact email to our Lucknow fleet. Our autonomous AI agent analyzes problem severity,
            estimates repair costs, and routes your request to verified technicians.
          </p>
        </div>

        <div className="contact-main-grid">
          {/* Left Column: Direct Info & AI Flow */}
          <div className="contact-info-panel">
            <div className="contact-card-highlight">
              <div className="highlight-icon">⚡</div>
              <div>
                <h3 className="highlight-title">Lucknow Fleet Dispatch Center</h3>
                <p className="highlight-desc">Average doorstep response time 20-30 minutes across all Lucknow zones.</p>
              </div>
            </div>

            <div className="contact-details-list">
              <div className="contact-detail-row">
                <span className="detail-icon">📞</span>
                <div>
                  <div className="detail-label">24/7 Emergency Dispatch Line</div>
                  <a href="tel:+919812345678" className="detail-value-link">+91 98123 45678</a>
                </div>
              </div>

              <div className="contact-detail-row">
                <span className="detail-icon">✉️</span>
                <div>
                  <div className="detail-label">Direct Support Email</div>
                  <a href="mailto:support@electrolko.in" className="detail-value-link">support@electrolko.in</a>
                </div>
              </div>

              <div className="contact-detail-row">
                <span className="detail-icon">🏢</span>
                <div>
                  <div className="detail-label">Head Operations Office</div>
                  <div className="detail-value">Vibhuti Khand, Gomti Nagar, Lucknow, UP 226010</div>
                </div>
              </div>
            </div>

            {/* AI Agent Workflow Explainer */}
            <div className="ai-workflow-box">
              <div className="ai-workflow-head">
                <span>🤖</span>
                <span>How Our AI Agent Handles Your Email</span>
              </div>
              <div className="ai-workflow-steps">
                <div className="ai-step-item">
                  <span className="ai-step-num">1</span>
                  <div>
                    <strong>Instant Hazard & Severity Triage</strong>
                    <p>Detects active sparking, shocks, or tripping to assign priority response.</p>
                  </div>
                </div>
                <div className="ai-step-item">
                  <span className="ai-step-num">2</span>
                  <div>
                    <strong>Diagnostic & Price Estimation</strong>
                    <p>Matches genuine parts catalog and standard upfront Lucknow rates.</p>
                  </div>
                </div>
                <div className="ai-step-item">
                  <span className="ai-step-num">3</span>
                  <div>
                    <strong>Live Dispatch Logging</strong>
                    <p>Creates a unique ticket ID and dispatches technical briefing to technician.</p>
                  </div>
                </div>
              </div>
            </div>

            <button
              type="button"
              className="btn-open-guru-alt"
              onClick={() => {
                setRobotQuery('I want to send an inquiry email to ElectroFix support');
                setIsRobotForceOpen(true);
              }}
            >
              <span>🤖</span> Talk with Bijli Guru Voice Assistant
            </button>
          </div>

          {/* Right Column: AI-Powered Contact Email Form */}
          <div className="contact-form-panel">
            {contactSuccess ? (
              <div className="contact-success-state">
                <div className="success-badge-icon">✅</div>
                <h3 className="success-title">Contact Email Successfully Dispatched!</h3>
                <p className="success-desc">
                  Your inquiry has been logged in our Lucknow database and dispatched to our technical fleet.
                </p>

                <div className="success-ticket-box">
                  <div className="ticket-row">
                    <span>Ticket Reference:</span>
                    <strong className="ticket-ref-text">{contactSuccess.ticket_reference}</strong>
                  </div>
                  <div className="ticket-row">
                    <span>Assigned Priority:</span>
                    <span className={`priority-pill ${contactSuccess.ai_priority?.toLowerCase() || 'normal'}`}>
                      {contactSuccess.ai_priority || 'NORMAL'}
                    </span>
                  </div>
                  <div className="ticket-row">
                    <span>Sender Email:</span>
                    <span>{contactSuccess.email}</span>
                  </div>
                  <div className="ticket-row">
                    <span>Locality Zone:</span>
                    <span>{contactSuccess.area}</span>
                  </div>
                  {contactSuccess.ai_diagnosis && (
                    <div className="ticket-diagnosis-box">
                      <div className="diag-title">🤖 AI Agent Diagnostic Report:</div>
                      <p>{contactSuccess.ai_diagnosis}</p>
                    </div>
                  )}
                </div>

                <div className="success-actions-row">
                  <button
                    type="button"
                    className="btn-send-another"
                    onClick={resetContactForm}
                  >
                    Send Another Email
                  </button>
                  <button
                    type="button"
                    className="btn-chat-ticket"
                    onClick={() => {
                      setRobotQuery(`Check status of contact ticket ${contactSuccess.ticket_reference}`);
                      setIsRobotForceOpen(true);
                    }}
                  >
                    <span>🤖</span> Track with Bijli Guru
                  </button>
                </div>
              </div>
            ) : (
              <form onSubmit={handleSendContactEmail} className="contact-email-form">
                <div className="form-head-title">
                  <h3>Send Email with AI Agent Assistant</h3>
                  <p>Type your message below and let our AI agent analyze urgency & diagnose the issue.</p>
                </div>

                {contactError && (
                  <div className="contact-error-alert">
                    <span>⚠️</span>
                    <span>{contactError}</span>
                  </div>
                )}

                <div className="form-fields-grid">
                  <div className="field-group">
                    <label>Your Full Name *</label>
                    <input
                      type="text"
                      required
                      placeholder="e.g. Ramesh Verma"
                      value={contactForm.name}
                      onChange={(e) => setContactForm({ ...contactForm, name: e.target.value })}
                    />
                  </div>

                  <div className="field-group">
                    <label>Your Email Address *</label>
                    <input
                      type="email"
                      required
                      placeholder="e.g. ramesh@gmail.com"
                      value={contactForm.email}
                      onChange={(e) => setContactForm({ ...contactForm, email: e.target.value })}
                    />
                  </div>
                </div>

                <div className="form-fields-grid">
                  <div className="field-group">
                    <label>Phone / WhatsApp (Optional)</label>
                    <input
                      type="tel"
                      placeholder="e.g. +91 98765 43210"
                      value={contactForm.phone}
                      onChange={(e) => setContactForm({ ...contactForm, phone: e.target.value })}
                    />
                  </div>

                  <div className="field-group">
                    <label>Locality in Lucknow</label>
                    <select
                      value={contactForm.area}
                      onChange={(e) => setContactForm({ ...contactForm, area: e.target.value })}
                    >
                      <option value="Gomti Nagar, Lucknow">Gomti Nagar</option>
                      <option value="Indira Nagar, Lucknow">Indira Nagar</option>
                      <option value="Hazratganj, Lucknow">Hazratganj</option>
                      <option value="Aliganj, Lucknow">Aliganj</option>
                      <option value="Alambagh, Lucknow">Alambagh</option>
                      <option value="Rajajipuram, Lucknow">Rajajipuram</option>
                      <option value="Chowk, Lucknow">Chowk</option>
                      <option value="Mahanagar, Lucknow">Mahanagar</option>
                      <option value="Ashiyana, Lucknow">Ashiyana</option>
                      <option value="Vikas Nagar, Lucknow">Vikas Nagar</option>
                    </select>
                  </div>
                </div>

                <div className="field-group">
                  <label>Subject / Topic (Optional - AI can generate)</label>
                  <input
                    type="text"
                    placeholder="e.g. MCB Tripping every 20 mins when AC starts"
                    value={contactForm.subject}
                    onChange={(e) => setContactForm({ ...contactForm, subject: e.target.value })}
                  />
                </div>

                <div className="field-group">
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '6px' }}>
                    <label style={{ margin: 0 }}>Describe the Problem / Message *</label>
                    <button
                      type="button"
                      className="btn-ai-enhance-trigger"
                      onClick={handleAiDraftContact}
                      disabled={contactAiLoading || !contactForm.message.trim()}
                      title="Run AI Agent Diagnostic & Polish"
                    >
                      {contactAiLoading ? '🤖 Analyzing...' : '✨ AI Agent Diagnose & Polish'}
                    </button>
                  </div>
                  <textarea
                    rows={4}
                    required
                    placeholder="Describe what happened (e.g. Living room switchboard sparking with burnt smell, or ceiling fan humming loudly and not running at full speed...)"
                    value={contactForm.message}
                    onChange={(e) => setContactForm({ ...contactForm, message: e.target.value })}
                  />
                </div>

                {/* AI Agent Diagnostic Preview Box */}
                {contactAiResult && (
                  <div className="contact-ai-preview-box">
                    <div className="preview-top-bar">
                      <div className="preview-title">
                        <span>🤖</span>
                        <strong>AI Agent Diagnostic Analysis</strong>
                      </div>
                      <span className={`priority-pill ${contactAiResult.ai_priority?.toLowerCase() || 'normal'}`}>
                        {contactAiResult.ai_priority || 'NORMAL'}
                      </span>
                    </div>

                    <p className="preview-diagnosis-text">{contactAiResult.ai_diagnosis}</p>

                    <div className="preview-meta-chips">
                      {contactAiResult.ai_recommended_service && (
                        <div className="meta-chip">
                          <span className="chip-label">Recommended:</span>
                          <span className="chip-val">{contactAiResult.ai_recommended_service}</span>
                        </div>
                      )}
                      {contactAiResult.ai_estimated_cost && (
                        <div className="meta-chip">
                          <span className="chip-label">Est. Cost:</span>
                          <span className="chip-val">{contactAiResult.ai_estimated_cost}</span>
                        </div>
                      )}
                    </div>

                    {contactAiResult.safety_advisory && (
                      <div className="preview-safety-alert">
                        <span>🛡️</span>
                        <span>{contactAiResult.safety_advisory}</span>
                      </div>
                    )}
                  </div>
                )}

                <div className="form-submit-row">
                  <button
                    type="submit"
                    className="btn-submit-contact-email"
                    disabled={contactSending}
                  >
                    {contactSending ? (
                      <>
                        <span className="btn-spinner">⚡</span>
                        <span>Dispatching Email via AI Agent...</span>
                      </>
                    ) : (
                      <>
                        <span>📨</span>
                        <span>Send Contact Email</span>
                      </>
                    )}
                  </button>
                </div>
              </form>
            )}
          </div>
        </div>
      </section>

      {/* 13. Site Footer */}
      <footer className="site-footer">
        <div className="footer-inner-grid">
          <div>
            <div style={{ display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '14px' }}>
              <span style={{ fontSize: '24px', background: '#f59e0b', color: 'white', borderRadius: '8px', padding: '2px 8px' }}>⚡</span>
              <span style={{ fontSize: '20px', fontWeight: '800', color: '#ffffff' }}>ElectroFix</span>
            </div>
            <p style={{ fontSize: '13.5px', lineHeight: '1.6', color: '#94a3b8', marginBottom: '16px' }}>
              Lucknow's premier verified on-demand electrical service network. Equipped with the
              Ollama-powered Bijli Guru AI Diagnostic engine to keep your home safe, powered, and efficient.
            </p>
            <div style={{ fontSize: '13px', color: '#38bdf8', fontWeight: '700' }}>
              24/7 Helpline: +91 98123 45678
            </div>
          </div>

          <div>
            <h4 className="footer-col-title">Popular Services</h4>
            <ul className="footer-links-list">
              <li><a href="#services">Ceiling Fan Repair (₹149)</a></li>
              <li><a href="#services">Modular Switchboards (₹99)</a></li>
              <li><a href="#services">MCB Tripping Diagnostic (₹199)</a></li>
              <li><a href="#services">Inverter & Battery Wiring (₹249)</a></li>
              <li><a href="#services">Full House Earthing Audit (₹499)</a></li>
              <li><a href="#services">Emergency Hazard Clearance (₹299)</a></li>
            </ul>
          </div>

          <div>
            <h4 className="footer-col-title">Lucknow Zones</h4>
            <ul className="footer-links-list">
              <li><a href="#technicians">Gomti Nagar & Phase 2</a></li>
              <li><a href="#technicians">Indira Nagar & Sector 14</a></li>
              <li><a href="#technicians">Hazratganj & Park Road</a></li>
              <li><a href="#technicians">Aliganj & Mahanagar</a></li>
              <li><a href="#technicians">Alambagh & Ashiyana</a></li>
              <li><a href="#technicians">Rajajipuram & Chowk</a></li>
            </ul>
          </div>

          <div>
            <h4 className="footer-col-title">Contact & Dispatch</h4>
            <div className="footer-contact-item">
              <span>📍</span>
              <span>Head Office: Vibhuti Khand, Gomti Nagar, Lucknow, UP 226010</span>
            </div>
            <div className="footer-contact-item">
              <span>✉️</span>
              <span>support@electrolko.in</span>
            </div>
            <div className="footer-contact-item">
              <span>⏱️</span>
              <span>Service Hours: 24/7 Round the Clock Dispatch</span>
            </div>
          </div>
        </div>

        <div className="footer-bottom-bar">
          <div>
            &copy; {new Date().getFullYear()} ElectroFix (ElectroLKO). All rights reserved. Powered by Ollama Local AI.
          </div>
          <div style={{ display: 'flex', gap: '16px', flexWrap: 'wrap' }}>
            <a href="#safety">Safety Guarantee</a>
            <a href="#risk-checker">Risk Checker</a>
            <a href="#packages">AMC Packages</a>
            <a href="#energy-guide">Tariff Guide</a>
            <a href="#commercial">Commercial & EV</a>
            <a href="#case-studies">Case Studies</a>
            <a href="#calculator">Estimator</a>
            <a href="#faq">FAQ</a>
          </div>
        </div>
      </footer>

      {/* 14. Quick Booking Modal Dialog */}
      {bookingModal.isOpen && (
        <div className="modal-backdrop" onClick={() => setBookingModal((p) => ({ ...p, isOpen: false }))}>
          <div className="modal-dialog-card" onClick={(e) => e.stopPropagation()}>
            <div className="modal-header-bar">
              <div className="modal-title-text">
                {bookingModal.successReference ? '✅ Booking Confirmed!' : '⚡ Book Electrician Visit'}
              </div>
              <button
                type="button"
                className="modal-close-btn"
                onClick={() => setBookingModal((p) => ({ ...p, isOpen: false }))}
              >
                ✕
              </button>
            </div>

            {bookingModal.successReference ? (
              <div style={{ padding: '28px 24px', textAlign: 'center' }}>
                <div style={{ fontSize: '48px', marginBottom: '12px' }}>🎉</div>
                <h3 style={{ fontSize: '20px', fontWeight: '800', color: '#0f172a', marginBottom: '8px' }}>
                  Your Technician is Dispatched!
                </h3>
                <div style={{ background: '#f8fafc', border: '1px dashed #cbd5e1', borderRadius: '12px', padding: '16px', margin: '16px 0', textAlign: 'left', fontSize: '13.5px' }}>
                  <div><strong>Booking ID:</strong> <span style={{ color: '#2563eb' }}>{bookingModal.successReference}</span></div>
                  <div><strong>Service:</strong> {bookingModal.service}</div>
                  <div><strong>Address:</strong> {bookingModal.customerAddress}</div>
                  <div><strong>Expected Arrival:</strong> ~20-25 Mins</div>
                  <div><strong>Assigned Pro:</strong> {bookingModal.electrician ? bookingModal.electrician.name : 'Nearest Verified Master'}</div>
                </div>
                <p style={{ fontSize: '13px', color: '#64748b', marginBottom: '20px' }}>
                  The technician will call your number shortly before arriving. Payment is only after completion.
                </p>
                <button
                  type="button"
                  className="btn-modal-submit"
                  style={{ width: '100%' }}
                  onClick={() => setBookingModal((p) => ({ ...p, isOpen: false }))}
                >
                  Done
                </button>
              </div>
            ) : (
              <form onSubmit={handleSubmitBooking} className="modal-form-body">
                {bookingModal.electrician && (
                  <div style={{ background: '#eff6ff', border: '1px solid #bfdbfe', borderRadius: '8px', padding: '10px 14px', fontSize: '13px', color: '#1e40af', fontWeight: '700' }}>
                    ⚡ Assigned Master: {bookingModal.electrician.name} (⭐ {bookingModal.electrician.rating || '4.9'}) • {bookingModal.electrician.distance || 'Nearby'}
                  </div>
                )}

                <div className="form-field-group">
                  <label className="form-label">Selected Service</label>
                  <input
                    type="text"
                    className="form-input"
                    value={bookingModal.service}
                    onChange={(e) => setBookingModal((p) => ({ ...p, service: e.target.value }))}
                    required
                  />
                </div>

                <div className="form-field-group">
                  <label className="form-label">Your Name</label>
                  <input
                    type="text"
                    className="form-input"
                    placeholder="e.g. Ramesh Kumar"
                    value={bookingModal.customerName}
                    onChange={(e) => setBookingModal((p) => ({ ...p, customerName: e.target.value }))}
                    required
                  />
                </div>

                <div className="form-field-group">
                  <label className="form-label">Phone Number (For technician call)</label>
                  <input
                    type="tel"
                    className="form-input"
                    placeholder="+91 98123 45678"
                    value={bookingModal.customerPhone}
                    onChange={(e) => setBookingModal((p) => ({ ...p, customerPhone: e.target.value }))}
                    required
                  />
                </div>

                <div className="form-field-group">
                  <label className="form-label">Lucknow Address / House No.</label>
                  <input
                    type="text"
                    className="form-input"
                    placeholder="House number, Street, Landmark"
                    value={bookingModal.customerAddress}
                    onChange={(e) => setBookingModal((p) => ({ ...p, customerAddress: e.target.value }))}
                    required
                  />
                </div>

                <div className="form-field-group">
                  <label className="form-label">Preferred Time Slot</label>
                  <select
                    className="form-select"
                    value={bookingModal.timeSlot}
                    onChange={(e) => setBookingModal((p) => ({ ...p, timeSlot: e.target.value }))}
                  >
                    <option value="Within 30 mins (Immediate)">Within 30 mins (Immediate Priority)</option>
                    <option value="Today Evening (4:00 PM - 7:00 PM)">Today Evening (4:00 PM - 7:00 PM)</option>
                    <option value="Tomorrow Morning (9:00 AM - 12:00 PM)">Tomorrow Morning (9:00 AM - 12:00 PM)</option>
                  </select>
                </div>

                <div className="modal-actions-footer">
                  <button
                    type="button"
                    className="btn-modal-cancel"
                    onClick={() => setBookingModal((p) => ({ ...p, isOpen: false }))}
                  >
                    Cancel
                  </button>
                  <button type="submit" className="btn-modal-submit">
                    Confirm Doorstep Visit
                  </button>
                </div>
              </form>
            )}
          </div>
        </div>
      )}

      {/* 15. ElectroFix AI Robot Assistant (Ollama Powered) */}
      <ElectroFixRobot
        customerLocation={{
          area: customerLocation.areaName,
          lat: customerLocation.latitude,
          lng: customerLocation.longitude
        }}
        onSelectElectrician={setSelectedElectrician}
        externalTriggerQuery={robotQuery}
        isForceOpen={isRobotForceOpen}
        onClose={() => {
          setIsRobotForceOpen(false);
          setRobotQuery('');
        }}
      />
    </div>
  );
}

export default Home;
