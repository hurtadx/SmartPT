import { Navigate } from 'react-router-dom';
import { useAuth } from '../../features/auth/hooks/useAuth';

export const GuestRoute = ({ children }) => {
  const { user, loading } = useAuth();

  // Mostrar loading mientras se verifica la autenticación
  if (loading) {
    return (
      <div className="page-container">
        <div className="loading-container">
          <div className="loading">Verificando autenticación...</div>
        </div>
      </div>
    );
  }

  // Si el usuario está autenticado, redirigir al home
  if (user) {
    return <Navigate to="/" replace />;
  }

  // Si no está autenticado, mostrar el componente
  return children;
};
