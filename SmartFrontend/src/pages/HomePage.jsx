import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../features/auth/hooks/useAuth';
import { surveyService } from '../features/survey/services/surveyService';

export const HomePage = () => {
  const { user, logout } = useAuth();
  const [surveyStatus, setSurveyStatus] = useState({ loading: true, completed: false });
  const [message, setMessage] = useState('');

  useEffect(() => {
    // Verificar si hay un mensaje de redirección
    const redirectMessage = sessionStorage.getItem('surveyMessage');
    if (redirectMessage) {
      setMessage(redirectMessage);
      sessionStorage.removeItem('surveyMessage');
      
      // Limpiar mensaje después de 5 segundos
      setTimeout(() => {
        setMessage('');
      }, 5000);
    }
  }, []);

  useEffect(() => {
    const checkSurveyStatus = async () => {
      if (!user) return;
      
      try {
        const response = await surveyService.checkStatus();
        
        setSurveyStatus({
          loading: false,
          completed: response.success && response.data.has_completed_survey
        });
      } catch (error) {
        console.error('Error checking survey status:', error);
        setSurveyStatus({ loading: false, completed: false });
      }
    };

    checkSurveyStatus();
  }, [user]);

  const handleLogout = () => {
    logout();
  };

  return (
    <div className="page-container">
      <div className="home-page">
        <header className="home-header">
          <div className="logo-title">
            <i className="fas fa-poll-h logo-icon"></i>
            <h1>SmartPT Survey</h1>
          </div>
          {user && (
            <div className="user-info">
              <span>Bienvenido, {user.name}</span>
              <button onClick={handleLogout} className="logout-btn">
                <i className="fas fa-sign-out-alt"></i>
                Cerrar Sesión
              </button>
            </div>
          )}
        </header>

        {/* Mensaje de notificación */}
        {message && (
          <div className="notification-message" style={{
            backgroundColor: '#fbbf24',
            color: '#92400e',
            padding: '1rem',
            margin: '1rem',
            borderRadius: '0.5rem',
            borderLeft: '4px solid #f59e0b',
            display: 'flex',
            alignItems: 'center',
            gap: '0.5rem'
          }}>
            <i className="fas fa-info-circle"></i>
            {message}
          </div>
        )}

        <main className="home-content">
          {user ? (
            <div className="dashboard">
              <h2>Panel de Control</h2>
              <div className="dashboard-actions">
                {/* Tarjeta de Encuesta */}
                {surveyStatus.completed ? (
                  <div className="dashboard-link disabled-link">
                    <div className="action-card completed">
                      <i className="fas fa-check-circle card-icon"></i>
                      <h3>Encuesta Completada</h3>
                      <p>Ya completaste la encuesta exitosamente.</p>
                    </div>
                  </div>
                ) : (
                  <Link 
                    to="/survey" 
                    className="dashboard-link"
                  >
                    <div className="action-card">
                      <i className="fas fa-clipboard-list card-icon"></i>
                      <h3>
                        {surveyStatus.loading ? 'Verificando...' : 'Llenar Encuesta'}
                      </h3>
                      <p>
                        {surveyStatus.loading ? 'Verificando estado de la encuesta...' :
                         'Completa la encuesta de desarrollo'}
                      </p>
                    </div>
                  </Link>
                )}
                
                {/* Tarjeta de Resultados */}
                {surveyStatus.completed ? (
                  <Link 
                    to="/results" 
                    className="dashboard-link"
                  >
                    <div className="action-card available">
                      <i className="fas fa-chart-bar card-icon"></i>
                      <h3>Ver Resultados</h3>
                      <p>Revisa tus respuestas enviadas</p>
                    </div>
                  </Link>
                ) : (
                  <div className="dashboard-link disabled-link">
                    <div className="action-card disabled">
                      <i className="fas fa-chart-bar card-icon"></i>
                      <h3>Ver Resultados</h3>
                      <p>
                        {surveyStatus.loading ? 'Verificando...' :
                         'Completa la encuesta primero'}
                      </p>
                    </div>
                  </div>
                )}
              </div>
            </div>
          ) : (
            <div className="welcome">
              <h2>Bienvenido a SmartPT Survey</h2>
              <p>Aplicación de encuestas para desarrolladores</p>
              <div className="auth-actions">
                <Link to="/login" className="btn btn-primary">
                  Iniciar Sesión
                </Link>
                <Link to="/register" className="btn btn-secondary">
                  Registrarse
                </Link>
              </div>
            </div>
          )}
        </main>
      </div>
    </div>
  );
};
