import React, { useState, useEffect } from 'react';
import Modal from '../../common/Modal';
import TonerForm from './TonerForm';
import TonerGrid from './TonerGrid';
import ApiService from '../../../services/api';
import './TonerCadastro.css';

const TonerCadastro = () => {
  const [toners, setToners] = useState([]);
  const [editingToner, setEditingToner] = useState(null);
  const [showForm, setShowForm] = useState(false);

  // Carregar toners da API
  useEffect(() => {
    loadToners();
  }, []);

  const loadToners = async () => {
    try {
      const tonersData = await ApiService.getToners();
      setToners(tonersData);
    } catch (error) {
      console.error('Erro ao carregar toners:', error);
      // Fallback para localStorage se API falhar
      const savedToners = localStorage.getItem('sgq-toners');
      if (savedToners) {
        try {
          setToners(JSON.parse(savedToners));
        } catch (parseError) {
          console.error('Erro ao carregar toners do localStorage:', parseError);
          localStorage.removeItem('sgq-toners'); // Remove o valor inválido
          setToners([]);
        }
      }
    }
  };

  const handleSaveToner = async (tonerData) => {
    try {
      if (editingToner) {
        // Editar toner existente
        const updatedToner = await ApiService.updateToner(editingToner.id, tonerData);
        setToners(toners.map(toner => 
          toner.id === editingToner.id ? updatedToner : toner
        ));
        setEditingToner(null);
      } else {
        // Adicionar novo toner
        const newToner = await ApiService.createToner(tonerData);
        setToners([...toners, newToner]);
      }
      setShowForm(false);
    } catch (error) {
      console.error('Erro ao salvar toner:', error);
      alert('Erro ao salvar toner: ' + error.message);
    }
  };

  const handleEditToner = (toner) => {
    setEditingToner(toner);
    setShowForm(true);
  };

  const handleDeleteToner = async (tonerId) => {
    if (window.confirm('Tem certeza que deseja excluir este toner?')) {
      try {
        await ApiService.deleteToner(tonerId);
        setToners(toners.filter(toner => toner.id !== tonerId));
      } catch (error) {
        console.error('Erro ao deletar toner:', error);
        alert('Erro ao deletar toner: ' + error.message);
      }
    }
  };

  const handleCancelForm = () => {
    setEditingToner(null);
    setShowForm(false);
  };

  return (
    <div className="toner-cadastro">
      <div className="cadastro-header">
        <div className="header-info">
          <h2>Cadastro de Toners</h2>
          <p>Gerencie o cadastro de toners e cartuchos</p>
        </div>
        <button 
          className="btn-primary"
          onClick={() => setShowForm(true)}
        >
          <span className="btn-icon">➕</span>
          Novo Toner
        </button>
      </div>

      <Modal
        isOpen={showForm}
        onClose={handleCancelForm}
        title={editingToner ? 'Editar Toner' : 'Novo Toner'}
        size="large"
      >
        <TonerForm
          toner={editingToner}
          onSave={handleSaveToner}
          onCancel={handleCancelForm}
        />
      </Modal>

      <div className="grid-section">
        <TonerGrid
          toners={toners}
          onEdit={handleEditToner}
          onDelete={handleDeleteToner}
        />
      </div>
    </div>
  );
};

export default TonerCadastro;
