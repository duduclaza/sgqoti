import React from 'react';
import TonersModule from './modules/TonersModule';
import './MainContent.css';

const MainContent = ({ activeModule }) => {
  // Renderizar módulos específicos
  if (activeModule === 'toners') {
    return (
      <div className="main-content">
        <div className="content-header">
          <h1>Controle de Toners</h1>
          <p>Gerenciamento e controle de toners da organização</p>
        </div>
        <div className="content-body">
          <TonersModule />
        </div>
      </div>
    );
  }

  const moduleContent = {
    'homologacoes': {
      title: 'Homologações',
      description: 'Processo de homologação de produtos e serviços',
      content: 'Gerenciamento de processos de homologação e aprovação.'
    },
    'amostragens': {
      title: 'Amostragens',
      description: 'Controle e análise de amostragens',
      content: 'Sistema para registro e acompanhamento de amostragens.'
    },
    'garantias': {
      title: 'Garantias',
      description: 'Gestão de garantias de produtos e equipamentos',
      content: 'Controle de prazos e condições de garantia.'
    },
    'pops-its': {
      title: 'POPs e ITs',
      description: 'Procedimentos Operacionais Padrão e Instruções de Trabalho',
      content: 'Biblioteca de procedimentos e instruções de trabalho.'
    },
    'fluxogramas': {
      title: 'Fluxogramas',
      description: 'Mapeamento de processos organizacionais',
      content: 'Visualização e gestão de fluxos de processo.'
    },
    'auditorias': {
      title: 'Auditorias',
      description: 'Planejamento e execução de auditorias',
      content: 'Sistema para gestão completa de auditorias internas e externas.'
    },
    'dinamicas': {
      title: 'Dinâmicas',
      description: 'Atividades dinâmicas e treinamentos',
      content: 'Gestão de atividades de capacitação e desenvolvimento.'
    },
    'configuracoes': {
      title: 'Configurações',
      description: 'Configurações do sistema',
      content: 'Personalização e configuração do sistema SGQ OTI.'
    }
  };

  const currentModule = moduleContent[activeModule] || moduleContent['toners'];

  return (
    <div className="main-content">
      <div className="content-header">
        <h1>{currentModule.title}</h1>
        <p>{currentModule.description}</p>
      </div>
      
      <div className="content-body">
        <div className="module-card">
          <div className="card-content">
            <p>{currentModule.content}</p>
            <div className="coming-soon">
              <span className="badge">Em Desenvolvimento</span>
              <p>Este módulo está sendo desenvolvido e estará disponível em breve.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default MainContent;
