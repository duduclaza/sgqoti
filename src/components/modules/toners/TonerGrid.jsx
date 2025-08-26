import React, { useState } from 'react';
import './TonerGrid.css';

const TonerGrid = ({ toners, onEdit, onDelete }) => {
  const [sortField, setSortField] = useState('modelo');
  const [sortDirection, setSortDirection] = useState('asc');
  const [searchTerm, setSearchTerm] = useState('');

  const handleSort = (field) => {
    if (sortField === field) {
      setSortDirection(sortDirection === 'asc' ? 'desc' : 'asc');
    } else {
      setSortField(field);
      setSortDirection('asc');
    }
  };

  const filteredToners = toners.filter(toner =>
    toner.modelo.toLowerCase().includes(searchTerm.toLowerCase()) ||
    toner.cor.toLowerCase().includes(searchTerm.toLowerCase()) ||
    toner.tipo.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const sortedToners = [...filteredToners].sort((a, b) => {
    let aValue = a[sortField];
    let bValue = b[sortField];

    if (typeof aValue === 'string') {
      aValue = aValue.toLowerCase();
      bValue = bValue.toLowerCase();
    }

    if (sortDirection === 'asc') {
      return aValue > bValue ? 1 : -1;
    } else {
      return aValue < bValue ? 1 : -1;
    }
  });

  const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    }).format(value);
  };

  const formatWeight = (value) => {
    return `${parseFloat(value).toFixed(2)}g`;
  };

  const getSortIcon = (field) => {
    if (sortField !== field) return '↕️';
    return sortDirection === 'asc' ? '↑' : '↓';
  };

  const getColorBadge = (cor) => {
    const colorMap = {
      'Black': { bg: '#000000', text: '#ffffff' },
      'Cyan': { bg: '#00bcd4', text: '#ffffff' },
      'Magenta': { bg: '#e91e63', text: '#ffffff' },
      'Yellow': { bg: '#ffeb3b', text: '#000000' }
    };
    
    const colors = colorMap[cor] || { bg: '#666666', text: '#ffffff' };
    
    return (
      <span 
        className="color-badge"
        style={{ backgroundColor: colors.bg, color: colors.text }}
      >
        {cor}
      </span>
    );
  };

  const getTipoBadge = (tipo) => {
    const typeMap = {
      'Original': 'type-original',
      'Compativel': 'type-compatible',
      'Remanufaturado': 'type-remanufactured'
    };
    
    return (
      <span className={`type-badge ${typeMap[tipo] || 'type-compatible'}`}>
        {tipo}
      </span>
    );
  };

  return (
    <div className="toner-grid-container">
      <div className="grid-header">
        <div className="grid-title">
          <h3>Toners Cadastrados</h3>
          <span className="grid-count">{filteredToners.length} registro(s)</span>
        </div>
        
        <div className="grid-search">
          <input
            type="text"
            placeholder="Pesquisar por modelo, cor ou tipo..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="search-input"
          />
          <span className="search-icon">🔍</span>
        </div>
      </div>

      {sortedToners.length === 0 ? (
        <div className="empty-state">
          <div className="empty-icon">📦</div>
          <h4>Nenhum toner encontrado</h4>
          <p>{searchTerm ? 'Tente ajustar os filtros de pesquisa' : 'Cadastre o primeiro toner para começar'}</p>
        </div>
      ) : (
        <div className="grid-table-container">
          <table className="toner-table">
            <thead>
              <tr>
                <th onClick={() => handleSort('modelo')} className="sortable">
                  Modelo {getSortIcon('modelo')}
                </th>
                <th onClick={() => handleSort('cor')} className="sortable">
                  Cor {getSortIcon('cor')}
                </th>
                <th onClick={() => handleSort('tipo')} className="sortable">
                  Tipo {getSortIcon('tipo')}
                </th>
                <th onClick={() => handleSort('gramatura')} className="sortable">
                  Gramatura {getSortIcon('gramatura')}
                </th>
                <th onClick={() => handleSort('capacidade')} className="sortable">
                  Capacidade {getSortIcon('capacidade')}
                </th>
                <th onClick={() => handleSort('gramaturafolha')} className="sortable">
                  g/Folha {getSortIcon('gramaturafolha')}
                </th>
                <th onClick={() => handleSort('preco')} className="sortable">
                  Preço {getSortIcon('preco')}
                </th>
                <th onClick={() => handleSort('precofolha')} className="sortable">
                  Preço/Folha {getSortIcon('precofolha')}
                </th>
                <th className="actions-column">Ações</th>
              </tr>
            </thead>
            <tbody>
              {sortedToners.map((toner) => (
                <tr key={toner.id}>
                  <td className="modelo-cell">
                    <strong>{toner.modelo}</strong>
                  </td>
                  <td>{getColorBadge(toner.cor)}</td>
                  <td>{getTipoBadge(toner.tipo)}</td>
                  <td>{formatWeight(toner.gramatura)}</td>
                  <td>{parseInt(toner.capacidade).toLocaleString()} folhas</td>
                  <td>{formatWeight(toner.gramaturafolha)}</td>
                  <td>{formatCurrency(toner.preco)}</td>
                  <td>{formatCurrency(toner.precofolha)}</td>
                  <td className="actions-cell">
                    <button
                      className="btn-action btn-edit"
                      onClick={() => onEdit(toner)}
                      title="Editar"
                    >
                      ✏️
                    </button>
                    <button
                      className="btn-action btn-delete"
                      onClick={() => onDelete(toner.id)}
                      title="Excluir"
                    >
                      🗑️
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
};

export default TonerGrid;
