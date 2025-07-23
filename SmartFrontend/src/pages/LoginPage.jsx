import { LoginForm } from '../features/auth/components/LoginForm';
import { Link } from 'react-router-dom';

export const LoginPage = () => {
  return (
    <div className="page-container">
      <div className="auth-page">
        <LoginForm />
        <div className="auth-links">
          <p>¿No tienes cuenta? <Link to="/register">Registrarse</Link></p>
        </div>
      </div>
    </div>
  );
};
