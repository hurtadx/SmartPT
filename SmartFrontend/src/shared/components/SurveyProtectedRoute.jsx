import { useState, useEffect } from 'react';
import { Navigate } from 'react-router-dom';
import { useAuth } from '../../features/auth/hooks/useAuth';
import { surveyService } from '../../features/survey/services/surveyService';

export const SurveyProtectedRoute = ({ children }) => {
  const { user, loading: authLoading } = useAuth();
  const [surveyStatus, setSurveyStatus] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const checkSurveyStatus = async () => {
      if (!user) {
        setLoading(false);
        return;
      }

      try {
        const status = await surveyService.checkStatus();
        setSurveyStatus(status);
      } catch (error) {
        console.error('Error checking survey status:', error);
        // En caso de error, permitir acceso por defecto
        setSurveyStatus({ has_completed_survey: false });
      } finally {
        setLoading(false);
      }
    };

    if (!authLoading) {
      checkSurveyStatus();
    }
  }, [user, authLoading]);

  // Mostrar loading mientras se verifica la autenticación o el estado de la encuesta
  if (authLoading || loading) {
    return (
      <div className="page-container">
        <div className="loading-container">
          <div className="loading">Verificando acceso a la encuesta...</div>
        </div>
      </div>
    );
  }

  // Si no está autenticado, redirigir al login
  if (!user) {
    return <Navigate to="/login" replace />;
  }

  // Si ya completó la encuesta, redirigir al home con mensaje
  if (surveyStatus?.has_completed_survey) {
    // Guardar mensaje temporal para mostrar en el home
    sessionStorage.setItem('surveyMessage', 'Ya has completado la encuesta. Puedes ver tus resultados en la sección correspondiente.');
    return <Navigate to="/" replace />;
  }

  // Si no ha completado la encuesta, mostrar el componente
  return children;
};
