import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from './features/auth';
import { ProtectedRoute, GuestRoute, SurveyProtectedRoute } from './shared';
import { HomePage } from './pages/HomePage';
import { LoginPage } from './pages/LoginPage';
import { RegisterPage } from './pages/RegisterPage';
import { SurveyPage } from './pages/SurveyPage';
import { ResultsPage } from './pages/ResultsPage';
import './App.css'

function App() {
  return (
    <AuthProvider>
      <Router>
        <div className="app">
          <Routes>
            <Route path="/" element={<HomePage />} />
            <Route 
              path="/login" 
              element={
                <GuestRoute>
                  <LoginPage />
                </GuestRoute>
              } 
            />
            <Route 
              path="/register" 
              element={
                <GuestRoute>
                  <RegisterPage />
                </GuestRoute>
              } 
            />
            <Route 
              path="/survey" 
              element={
                <SurveyProtectedRoute>
                  <SurveyPage />
                </SurveyProtectedRoute>
              } 
            />
            <Route 
              path="/results" 
              element={
                <ProtectedRoute>
                  <ResultsPage />
                </ProtectedRoute>
              } 
            />
            <Route path="*" element={<Navigate to="/" replace />} />
          </Routes>
          
          {/* Footer global */}
          <div className="app-footer">
            <p className="credit">Hecho por Andres Hurtado Molina</p>
          </div>
        </div>
      </Router>
    </AuthProvider>
  )
}

export default App
