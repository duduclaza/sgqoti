import React from 'react';
import './Sidebar.css';

const Sidebar = ({ activeModule, setActiveModule, modules }) => {
  return (
    <div className="sidebar">
      <div className="sidebar-header">
        <h3>Módulos</h3>
      </div>
      
      <nav className="sidebar-nav">
        <ul className="nav-list">
          {modules.map((module) => (
            <li key={module.id} className="nav-item">
              <button
                className={`nav-button ${activeModule === module.id ? 'active' : ''}`}
                onClick={() => setActiveModule(module.id)}
              >
                <span className="nav-icon">{module.icon}</span>
                <span className="nav-text">{module.name}</span>
              </button>
            </li>
          ))}
        </ul>
      </nav>
    </div>
  );
};

export default Sidebar;
