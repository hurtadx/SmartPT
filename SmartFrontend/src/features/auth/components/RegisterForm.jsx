import { useState } from 'react';
import { useAuth } from '../hooks/useAuth';

export const RegisterForm = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  });
  const [errors, setErrors] = useState({});
  const [isLoading, setIsLoading] = useState(false);
  
  const { register } = useAuth();

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
    
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: ''
      }));
    }
  };

  const validateForm = () => {
    const newErrors = {};
    
    if (!formData.name.trim()) {
      newErrors.name = 'El nombre es requerido';
    }
    
    if (!formData.email.trim()) {
      newErrors.email = 'El correo electrónico es requerido';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = 'El correo electrónico no es válido';
    }
    
    if (!formData.password) {
      newErrors.password = 'La contraseña es requerida';
    } else if (formData.password.length < 6) {
      newErrors.password = 'La contraseña debe tener al menos 6 caracteres';
    }
    
    if (!formData.password_confirmation) {
      newErrors.password_confirmation = 'La confirmación de contraseña es requerida';
    } else if (formData.password !== formData.password_confirmation) {
      newErrors.password_confirmation = 'Las contraseñas no coinciden';
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
      const result = await register(formData);
      if (!result.success) {
        setErrors({ 
          submit: result.message || 'Error al registrar usuario',
          ...result.errors 
        });
      }
    } catch (error) {
      setErrors({ submit: error.message || 'Error al registrar usuario' });
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="auth-form-container">
      <div className="auth-header">
        <i className="fas fa-user-plus auth-icon"></i>
        <h2>Crear Cuenta</h2>
      </div>
      <form onSubmit={handleSubmit} className="auth-form">
        <div className="form-group">
          <label htmlFor="name">Nombre</label>
          <input
            type="text"
            id="name"
            name="name"
            value={formData.name}
            onChange={handleChange}
            className={errors.name ? 'error' : ''}
            disabled={isLoading}
          />
          {errors.name && <span className="error-message">{errors.name}</span>}
        </div>

        <div className="form-group">
          <label htmlFor="email">Correo Electrónico</label>
          <input
            type="email"
            id="email"
            name="email"
            value={formData.email}
            onChange={handleChange}
            className={errors.email ? 'error' : ''}
            disabled={isLoading}
          />
          {errors.email && <span className="error-message">{errors.email}</span>}
        </div>

        <div className="form-group">
          <label htmlFor="password">Contraseña</label>
          <input
            type="password"
            id="password"
            name="password"
            value={formData.password}
            onChange={handleChange}
            className={errors.password ? 'error' : ''}
            disabled={isLoading}
          />
          {errors.password && <span className="error-message">{errors.password}</span>}
        </div>

        <div className="form-group">
          <label htmlFor="password_confirmation">Confirmar Contraseña</label>
          <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            value={formData.password_confirmation}
            onChange={handleChange}
            className={errors.password_confirmation ? 'error' : ''}
            disabled={isLoading}
          />
          {errors.password_confirmation && <span className="error-message">{errors.password_confirmation}</span>}
        </div>

        {errors.submit && <div className="error-message">{errors.submit}</div>}

        <button type="submit" disabled={isLoading} className="submit-btn">
          <i className="fas fa-user-plus"></i>
          {isLoading ? 'Registrando...' : 'Registrarse'}
        </button>
      </form>
    </div>
  );
};
