import { useState, useEffect } from 'react';
import { Navigate } from 'react-router-dom';
import { useAuth } from '../../features/auth/hooks/useAuth';
import { surveyService } from '../../features/survey/services/surveyService';

export const SurveyProtectedRoute = ({ children }) => {
  const { user, loading: authLoading } = useAuth();
  const [surveyStatus, setSurveyStatus] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const checkSurveyStatus = async () => {
      if (!user) {
        setLoading(false);
        return;
      }

      try {
        setError(null);
        const status = await surveyService.checkStatus();
        setSurveyStatus(status);
        
        // Doble verificación: si la respuesta indica que ya completó, redirigir inmediatamente
        if (status?.data?.has_completed_survey || status?.has_completed_survey) {
          sessionStorage.setItem('surveyMessage', 'Ya has completado la encuesta. No puedes acceder nuevamente al formulario.');
          setLoading(false);
          return;
        }
        
      } catch (error) {
        console.error('Error checking survey status:', error);
        setError('Error al verificar el estado de la encuesta');
        
        // En caso de error de conectividad, bloquear acceso por seguridad
        // Solo permitir si explícitamente sabemos que no ha completado
        setSurveyStatus({ has_completed_survey: true }); // Bloquear por defecto
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
  if (surveyStatus?.has_completed_survey || surveyStatus?.data?.has_completed_survey) {
    // Guardar mensaje temporal para mostrar en el home
    sessionStorage.setItem('surveyMessage', 'Ya has completado la encuesta. No puedes acceder nuevamente al formulario.');
    return <Navigate to="/" replace />;
  }

  // Si hay error en la verificación, redirigir con mensaje de error
  if (error) {
    sessionStorage.setItem('surveyMessage', 'Error al verificar el estado de la encuesta. Inténtalo más tarde.');
    return <Navigate to="/" replace />;
  }

  // Si no ha completado la encuesta, mostrar el componente
  return children;
};
