import { Navigate } from 'react-router-dom';
import { useAuth } from '../../features/auth/hooks/useAuth';

export const ProtectedRoute = ({ children }) => {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div className="loading-container">
        <div className="loading">Verificando autenticación...</div>
      </div>
    );
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  return children;
};
