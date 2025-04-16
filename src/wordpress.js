
import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './App.tsx';
import './index.css';

// Admin initialization
document.addEventListener('DOMContentLoaded', function() {
  const adminContainer = document.getElementById('form-stash-admin');
  
  if (adminContainer) {
    createRoot(adminContainer).render(
      <React.StrictMode>
        <App />
      </React.StrictMode>
    );
  }
});

// Frontend form initialization
document.addEventListener('DOMContentLoaded', function() {
  const formContainers = document.querySelectorAll('.form-stash-container');
  
  if (formContainers.length > 0) {
    formContainers.forEach(container => {
      const formId = container.id.replace('form-stash-form-', '');
      
      createRoot(container).render(
        <React.StrictMode>
          <FormRenderer formId={formId} />
        </React.StrictMode>
      );
    });
  }
});

// Simple form renderer component for frontend
function FormRenderer({ formId }) {
  const [form, setForm] = React.useState(null);
  const [loading, setLoading] = React.useState(true);
  const [error, setError] = React.useState(null);
  const [formData, setFormData] = React.useState({});
  const [submitted, setSubmitted] = React.useState(false);
  const [submitMessage, setSubmitMessage] = React.useState('');
  
  React.useEffect(() => {
    const fetchForm = async () => {
      try {
        const response = await fetch(
          `${window.formStashData.apiUrl}/forms/${formId}`,
          {
            headers: {
              'X-WP-Nonce': window.formStashData.nonce
            }
          }
        );
        
        if (!response.ok) {
          throw new Error('Failed to load form');
        }
        
        const data = await response.json();
        data.fields = JSON.parse(data.fields);
        setForm(data);
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };
    
    fetchForm();
  }, [formId]);
  
  const handleSubmit = async (e) => {
    e.preventDefault();
    
    try {
      const response = await fetch(
        `${window.formStashData.apiUrl}/submit`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': window.formStashData.nonce
          },
          body: JSON.stringify({
            formId: parseInt(formId),
            data: formData
          })
        }
      );
      
      if (!response.ok) {
        throw new Error('Failed to submit form');
      }
      
      const result = await response.json();
      setSubmitted(true);
      setSubmitMessage(result.message);
    } catch (err) {
      setError(err.message);
    }
  };
  
  const handleInputChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }));
  };
  
  if (loading) {
    return <div>Loading form...</div>;
  }
  
  if (error) {
    return <div className="error">{error}</div>;
  }
  
  if (submitted) {
    return (
      <div className="form-success">
        <p>{submitMessage}</p>
      </div>
    );
  }
  
  if (!form) {
    return <div>Form not found</div>;
  }
  
  return (
    <div className="form-stash-form">
      <h3 className="form-title">{form.name}</h3>
      <form onSubmit={handleSubmit}>
        {form.fields.map(field => (
          <div key={field.id} className="form-field">
            {field.type !== 'checkbox' && (
              <label htmlFor={field.id}>
                {field.label}
                {field.required && <span className="required">*</span>}
              </label>
            )}
            
            {field.type === 'text' && (
              <input 
                type="text"
                id={field.id}
                name={field.id}
                placeholder={field.placeholder}
                required={field.required}
                onChange={handleInputChange}
              />
            )}
            
            {field.type === 'email' && (
              <input 
                type="email"
                id={field.id}
                name={field.id}
                placeholder={field.placeholder}
                required={field.required}
                onChange={handleInputChange}
              />
            )}
            
            {field.type === 'textarea' && (
              <textarea
                id={field.id}
                name={field.id}
                placeholder={field.placeholder}
                required={field.required}
                onChange={handleInputChange}
              />
            )}
            
            {field.type === 'select' && (
              <select
                id={field.id}
                name={field.id}
                required={field.required}
                onChange={handleInputChange}
              >
                <option value="">{field.placeholder || 'Select an option'}</option>
                {field.options?.map((option) => (
                  <option key={option} value={option}>
                    {option}
                  </option>
                ))}
              </select>
            )}
            
            {field.type === 'checkbox' && (
              <div className="checkbox-field">
                <input
                  type="checkbox"
                  id={field.id}
                  name={field.id}
                  required={field.required}
                  onChange={handleInputChange}
                />
                <label htmlFor={field.id}>
                  {field.label}
                  {field.required && <span className="required">*</span>}
                </label>
              </div>
            )}
          </div>
        ))}
        
        <button type="submit" className="submit-button">Submit</button>
      </form>
    </div>
  );
}
