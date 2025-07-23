import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { SurveyForm } from '../features/survey/components/SurveyForm';
import { surveyService } from '../features/survey/services/surveyService';
import { useAuth } from '../features/auth/hooks/useAuth';

export const SurveyPage = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [isCheckingStatus, setIsCheckingStatus] = useState(true);
  const [hasCompleted, setHasCompleted] = useState(false);

  useEffect(() => {
    const checkSurveyStatus = async () => {
      try {
        setIsCheckingStatus(true);
        const response = await surveyService.checkStatus();
        
        if (response.success && response.data.hasCompleted) {
          setHasCompleted(true);
          // Redirigir automáticamente a resultados después de 3 segundos
          setTimeout(() => {
            navigate('/results', { replace: true });
          }, 3000);
        }
      } catch (error) {
        console.error('Error checking survey status:', error);
        // Si hay error al verificar el estado, permitir que continue (mejor UX)
      } finally {
        setIsCheckingStatus(false);
      }
    };

    if (user) {
      checkSurveyStatus();
    }
  }, [user, navigate]);

  // Mostrar loading mientras verifica
  if (isCheckingStatus) {
    return (
      <div className="page-container">
        <div className="survey-container">
          <div className="loading-container">
            <div className="loading">Verificando estado de la encuesta...</div>
          </div>
        </div>
      </div>
    );
  }

  // Si ya completó la encuesta, mostrar mensaje y redirigir
  if (hasCompleted) {
    return (
      <div className="page-container">
        <div className="survey-container">
          <div className="survey-already-completed">
            <h2>Encuesta ya completada</h2>
            <p>Ya has completado esta encuesta anteriormente.</p>
            <p>Serás redirigido a tus resultados en unos segundos...</p>
            <div className="redirect-actions">
              <button 
                onClick={() => navigate('/results')}
                className="btn-primary"
              >
                Ver Resultados Ahora
              </button>
              <button 
                onClick={() => navigate('/')}
                className="btn-secondary"
              >
                Ir al Inicio
              </button>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // Solo mostrar el formulario si no ha completado la encuesta
  return (
    <div className="page-container">
      <SurveyForm />
    </div>
  );
};
