import { RegisterForm } from '../features/auth/components/RegisterForm';
import { Link } from 'react-router-dom';

export const RegisterPage = () => {
  return (
    <div className="page-container">
      <div className="auth-page">
        <RegisterForm />
        <div className="auth-links">
          <p>¿Ya tienes cuenta? <Link to="/login">Iniciar Sesión</Link></p>
        </div>
      </div>
    </div>
  );
};
