import api from '../../../shared/services/api';

export const surveyService = {
  getQuestions: async () => {
    try {
      const response = await api.get('/survey/questions');
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error de conexión' };
    }
  },

  submitSurvey: async (answers) => {
    try {
      const response = await api.post('/survey/submit', answers);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error de conexión' };
    }
  },

  getResults: async () => {
    try {
      const response = await api.get('/survey/results');
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error de conexión' };
    }
  },

  checkStatus: async () => {
    try {
      const response = await api.get('/survey/status');
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error de conexión' };
    }
  }
};
