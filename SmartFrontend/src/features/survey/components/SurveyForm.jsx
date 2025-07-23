import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../auth/hooks/useAuth';
import { surveyService } from '../services/surveyService';

export const SurveyForm = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(false);
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [errors, setErrors] = useState({});
  
  const [answers, setAnswers] = useState({
    favorite_framework: '', // Pregunta 1: texto abierto
    experience_level: '', // Pregunta 2: opción única
    programming_languages: [], // Pregunta 3: selección múltiple
    teamwork_rating: 3, // Pregunta 4: escala numérica
    agile_experience: null // Pregunta 5: Sí/No (boolean)
  });

  const handleTextareaChange = (field, value) => {
    setAnswers(prev => ({ ...prev, [field]: value }));
    if (errors[field]) {
      setErrors(prev => ({ ...prev, [field]: '' }));
    }
  };

  const handleRadioChange = (field, value) => {
    setAnswers(prev => ({ ...prev, [field]: value }));
    if (errors[field]) {
      setErrors(prev => ({ ...prev, [field]: '' }));
    }
  };

  const handleCheckboxChange = (field, option) => {
    setAnswers(prev => {
      const currentArray = prev[field];
      const newArray = currentArray.includes(option)
        ? currentArray.filter(item => item !== option)
        : [...currentArray, option];
      return { ...prev, [field]: newArray };
    });
    if (errors[field]) {
      setErrors(prev => ({ ...prev, [field]: '' }));
    }
  };

  const handleRangeChange = (field, value) => {
    setAnswers(prev => ({ ...prev, [field]: parseInt(value) }));
  };

  const validateForm = () => {
    const newErrors = {};

    if (!answers.favorite_framework.trim()) {
      newErrors.favorite_framework = 'Esta pregunta es requerida';
    }

    if (!answers.experience_level) {
      newErrors.experience_level = 'Selecciona tu nivel de experiencia';
    }

    if (answers.programming_languages.length === 0) {
      newErrors.programming_languages = 'Selecciona al menos un lenguaje';
    }

    if (answers.agile_experience === null) {
      newErrors.agile_experience = 'Esta pregunta es requerida';
    }

    return newErrors;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    const validationErrors = validateForm();
    if (Object.keys(validationErrors).length > 0) {
      setErrors(validationErrors);
      return;
    }

    setIsLoading(true);
    try {
      await surveyService.submitSurvey(answers);
      setIsSubmitted(true);
    } catch (error) {
      // Manejar específicamente el error de encuesta ya completada
      if (error.response?.status === 409) {
        setErrors({ submit: 'Ya has completado esta encuesta anteriormente. Puedes ver tus resultados en la sección de resultados.' });
      } else if (error.response?.status === 422) {
        setErrors({ submit: 'Ya has completado esta encuesta anteriormente. No es posible enviarla nuevamente.' });
      } else {
        setErrors({ submit: error.message || 'Error al enviar la encuesta' });
      }
    } finally {
      setIsLoading(false);
    }
  };

  // Redirección automática después de enviar la encuesta exitosamente
  useEffect(() => {
    if (isSubmitted) {
      const timer = setTimeout(() => {
        navigate('/');
      }, 3000); // Redirigir después de 3 segundos

      return () => clearTimeout(timer);
    }
  }, [isSubmitted, navigate]);

  if (isSubmitted) {
    return (
      <div className="survey-success">
        <div style={{ textAlign: 'center' }}>
          <i className="fas fa-check-circle" style={{ fontSize: '3rem', color: '#22c55e', marginBottom: '1rem' }}></i>
          <h2>¡Encuesta enviada exitosamente!</h2>
          <p>Gracias por completar la encuesta. Puedes ver tus respuestas en la sección de resultados.</p>
          <p style={{ color: '#6b7280', fontSize: '0.9rem', marginTop: '1rem' }}>
            <i className="fas fa-clock"></i> Regresando a la página principal en 3 segundos...
          </p>
        </div>
      </div>
    );
  }

  return (
    <div className="survey-container">
      <h1>Encuesta de Desarrollo</h1>
      <p>Hola {user?.name}, por favor completa la siguiente encuesta:</p>
      
      <form onSubmit={handleSubmit} className="survey-form">
        {/* Pregunta 1: Texto abierto */}
        <div className="question-group">
          <label>1. ¿Cuál es tu framework favorito y por qué?</label>
          <textarea
            value={answers.favorite_framework}
            onChange={(e) => handleTextareaChange('favorite_framework', e.target.value)}
            placeholder="Escribe tu respuesta aquí..."
            rows={4}
            className={errors.favorite_framework ? 'error' : ''}
            disabled={isLoading}
          />
          {errors.favorite_framework && (
            <span className="error-message">{errors.favorite_framework}</span>
          )}
        </div>

        {/* Pregunta 2: Opción única (Radio buttons) */}
        <div className="question-group">
          <label>2. ¿Cuál es tu nivel de experiencia en React?</label>
          <div className="radio-options">
            {['Junior', 'Mid', 'Senior'].map(level => (
              <label key={level} className="radio-option">
                <input
                  type="radio"
                  name="experience_level"
                  value={level}
                  checked={answers.experience_level === level}
                  onChange={(e) => handleRadioChange('experience_level', e.target.value)}
                  disabled={isLoading}
                />
                {level}
              </label>
            ))}
          </div>
          {errors.experience_level && (
            <span className="error-message">{errors.experience_level}</span>
          )}
        </div>

        {/* Pregunta 3: Selección múltiple (Checkboxes) */}
        <div className="question-group">
          <label>3. ¿Qué lenguajes de programación conoces?</label>
          <div className="checkbox-options">
            {['JavaScript', 'PHP', 'Python', 'Java'].map(language => (
              <label key={language} className="checkbox-option">
                <input
                  type="checkbox"
                  value={language}
                  checked={answers.programming_languages.includes(language)}
                  onChange={() => handleCheckboxChange('programming_languages', language)}
                  disabled={isLoading}
                />
                {language}
              </label>
            ))}
          </div>
          {errors.programming_languages && (
            <span className="error-message">{errors.programming_languages}</span>
          )}
        </div>

        {/* Pregunta 4: Escala numérica (Range) */}
        <div className="question-group">
          <label>4. En una escala del 1 al 5, ¿qué tanto te gusta trabajar en equipo?</label>
          <div className="range-container">
            <span>1</span>
            <input
              type="range"
              min="1"
              max="5"
              value={answers.teamwork_rating}
              onChange={(e) => handleRangeChange('teamwork_rating', e.target.value)}
              disabled={isLoading}
            />
            <span>5</span>
          </div>
          <div className="range-value">Valor seleccionado: {answers.teamwork_rating}</div>
        </div>

        {/* Pregunta 5: Sí/No (Radio buttons) */}
        <div className="question-group">
          <label>5. ¿Has trabajado con metodologías ágiles?</label>
          <div className="radio-options">
            {[
              { value: true, label: 'Sí' },
              { value: false, label: 'No' }
            ].map(option => (
              <label key={option.value} className="radio-option">
                <input
                  type="radio"
                  name="agile_experience"
                  value={option.value}
                  checked={answers.agile_experience === option.value}
                  onChange={() => handleRadioChange('agile_experience', option.value)}
                  disabled={isLoading}
                />
                {option.label}
              </label>
            ))}
          </div>
          {errors.agile_experience && (
            <span className="error-message">{errors.agile_experience}</span>
          )}
        </div>

        {errors.submit && <div className="error-message">{errors.submit}</div>}

        <button type="submit" disabled={isLoading} className="submit-btn">
          {isLoading ? 'Enviando...' : 'Enviar Encuesta'}
        </button>
      </form>
    </div>
  );
};
