import { Link } from 'react-router-dom';

function Login() {
  return (
    <div style={{ maxWidth: '600px', margin: '50px auto', fontFamily: 'sans-serif', textAlign: 'center' }}>
      <h1>Login</h1>
      <p>Login page placeholder (auth will be implemented later).</p>
      <div style={{ marginTop: '20px' }}>
        <Link to="/" style={{ color: '#0066cc', textDecoration: 'none', fontWeight: 'bold' }}>
          &larr; Back to Home
        </Link>
      </div>
    </div>
  );
}

export default Login;
