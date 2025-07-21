import api from '../../../shared/services/api';

// Servicios de encuestas
export const surveyService = {
  // Obtener preguntas de la encuesta
  getQuestions: async () => {
    try {
      const response = await api.get('/survey/questions');
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error de conexión' };
    }
  },

  // Enviar respuestas de la encuesta
  submitSurvey: async (answers) => {
    try {
      const response = await api.post('/survey/submit', answers);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error de conexión' };
    }
  },

  // Obtener resultados de la encuesta
  getResults: async () => {
    try {
      const response = await api.get('/survey/results');
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error de conexión' };
    }
  },

  // Verificar estado de la encuesta
  checkStatus: async () => {
    try {
      const response = await api.get('/survey/status');
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error de conexión' };
    }
  }
};
