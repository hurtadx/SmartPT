import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../auth/hooks/useAuth';
import { surveyService } from '../services/surveyService';

export const SurveyResults = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [results, setResults] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchResults = async () => {
      try {
        setIsLoading(true);
        const response = await surveyService.getResults();
        
        if (response.success && response.data) {
          setResults(response.data);
        } else {
          setError('No se pudieron cargar los resultados');
        }
      } catch (err) {
        console.error('Error loading survey results:', err);
        setError(err.message || 'Error al cargar los resultados');
      } finally {
        setIsLoading(false);
      }
    };

    fetchResults();
  }, []);

  if (isLoading) {
    return (
      <div className="results-container">
        <div className="loading">Cargando resultados...</div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="results-container">
        <div className="error-message">{error}</div>
      </div>
    );
  }

  if (!results) {
    return (
      <div className="results-container">
        <h1>Resultados de tu Encuesta</h1>
        <p>Aún no has completado la encuesta.</p>
        <p>Ve a la sección de encuesta para llenarla.</p>
      </div>
    );
  }

  return (
    <div className="results-container">
      <h1>Resultados de tu Encuesta</h1>
      <p>Hola {user?.name}, aquí están las respuestas que proporcionaste:</p>
      
      <div className="results-summary">
        <div className="result-header">
          <h2>Resumen de Respuestas</h2>
          <p className="submission-date">
            Completada el: {new Date(results.survey_info?.completed_at).toLocaleDateString('es-ES', {
              year: 'numeric',
              month: 'long',
              day: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            })}
          </p>
        </div>

        <div className="questions-results">
          {results?.results && results.results.map((result, index) => (
            <div key={index} className="result-item">
              <div className="question-number">Pregunta {index + 1}</div>
              <div className="question-title">{result.question}</div>
              <div className="answer-content">
                {result.type === 'text' ? (
                  <div className="long-answer">{result.answer}</div>
                ) : result.type === 'multiple' ? (
                  <div className="short-answer">{Array.isArray(result.answer) ? result.answer.join(', ') : result.answer}</div>
                ) : (
                  <div className="short-answer">{result.answer}</div>
                )}
              </div>
            </div>
          ))}
        </div>

        <div className="results-actions">
          <button 
            onClick={() => navigate(-1)} 
            className="back-btn"
            style={{
              backgroundColor: '#6b7280',
              color: 'white',
              padding: '0.75rem 1.5rem',
              border: 'none',
              borderRadius: '0.5rem',
              cursor: 'pointer',
              display: 'flex',
              alignItems: 'center',
              gap: '0.5rem',
              fontSize: '1rem',
              fontWeight: '500',
              transition: 'background-color 0.2s'
            }}
            onMouseOver={(e) => e.target.style.backgroundColor = '#4b5563'}
            onMouseOut={(e) => e.target.style.backgroundColor = '#6b7280'}
          >
            <i className="fas fa-arrow-left"></i>
            Volver
          </button>
        </div>
      </div>
    </div>
  );
};
