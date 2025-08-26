import React from 'react';
import { useTheme } from '../contexts/ThemeContext';
import './Header.css';

const Header = ({ activeModule, modules }) => {
  const { isDark, toggleTheme } = useTheme();
  
  const currentModule = modules.find(m => m.id === activeModule);

  return (
    <header className="app-header">
      <div className="header-left">
        <div className="company-logo">
          <div className="logo-icon">SGQ</div>
          <div className="company-info">
            <h1>SGQ OTI</h1>
            <span>Sistema de Gestão da Qualidade</span>
          </div>
        </div>
      </div>

      <div className="header-center">
        <div className="breadcrumb">
          <span className="breadcrumb-item">Home</span>
          <span className="breadcrumb-separator">›</span>
          <span className="breadcrumb-item active">{currentModule?.name || 'Dashboard'}</span>
        </div>
      </div>

      <div className="header-right">
        <div className="header-actions">
          <button className="action-btn" title="Notificações">
            <span className="icon">🔔</span>
            <span className="badge">3</span>
          </button>
          
          <button className="action-btn" title="Ajuda">
            <span className="icon">❓</span>
          </button>
          
          <button 
            className="theme-toggle-btn"
            onClick={toggleTheme}
            title={isDark ? 'Modo Claro' : 'Modo Escuro'}
          >
            <span className="icon">{isDark ? '☀️' : '🌙'}</span>
          </button>
          
          <div className="user-menu">
            <div className="user-avatar">
              <span>U</span>
            </div>
            <div className="user-info">
              <span className="user-name">Usuário</span>
              <span className="user-role">Administrador</span>
            </div>
            <span className="dropdown-arrow">▼</span>
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header;
