import React from 'react';
import { AuthProvider } from './features/auth';
import './App.css'

function App() {
  return (
    <AuthProvider>
      <div className="app">
        <h1>SmartPT Survey App</h1>
        <p>Aplicación de encuestas con React + Laravel</p>
      </div>
    </AuthProvider>
  )
}

export default App
