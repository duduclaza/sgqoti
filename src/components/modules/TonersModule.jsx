import React, { useState } from 'react';
import TonerCadastro from './toners/TonerCadastro';
import TonerRetornados from './toners/TonerRetornados';
import './TonersModule.css';

const TonersModule = () => {
  const [activeTab, setActiveTab] = useState('cadastro');

  const tabs = [
    { id: 'cadastro', name: 'Cadastro de Toners', icon: '📝' },
    { id: 'retornados', name: 'Registro de Retornados', icon: '↩️' }
  ];

  return (
    <div className="toners-module">
      <div className="module-tabs">
        {tabs.map((tab) => (
          <button
            key={tab.id}
            className={`tab-button ${activeTab === tab.id ? 'active' : ''}`}
            onClick={() => setActiveTab(tab.id)}
          >
            <span className="tab-icon">{tab.icon}</span>
            <span className="tab-name">{tab.name}</span>
          </button>
        ))}
      </div>

      <div className="tab-content">
        {activeTab === 'cadastro' && <TonerCadastro />}
        {activeTab === 'retornados' && <TonerRetornados />}
      </div>
    </div>
  );
};

export default TonersModule;
