import React, { useState, useEffect } from 'react';
import './TonerForm.css';

const TonerForm = ({ toner, onSave, onCancel }) => {
  const [formData, setFormData] = useState({
    modelo: '',
    pesocheio: '',
    pesovazio: '',
    gramatura: 0,
    capacidade: '',
    gramaturafolha: 0,
    preco: '',
    precofolha: 0,
    cor: 'Black',
    tipo: 'Compativel'
  });

  const [errors, setErrors] = useState({});

  useEffect(() => {
    if (toner) {
      setFormData(toner);
    }
  }, [toner]);

  // Calcular gramatura automaticamente
  useEffect(() => {
    const pesocheio = parseFloat(formData.pesocheio) || 0;
    const pesovazio = parseFloat(formData.pesovazio) || 0;
    const gramatura = pesocheio - pesovazio;
    
    setFormData(prev => ({
      ...prev,
      gramatura: gramatura > 0 ? gramatura : 0
    }));
  }, [formData.pesocheio, formData.pesovazio]);

  // Calcular gramatura por folha automaticamente
  useEffect(() => {
    const gramatura = parseFloat(formData.gramatura) || 0;
    const capacidade = parseFloat(formData.capacidade) || 0;
    const gramaturafolha = capacidade > 0 ? gramatura / capacidade : 0;
    
    setFormData(prev => ({
      ...prev,
      gramaturafolha: gramaturafolha
    }));
  }, [formData.gramatura, formData.capacidade]);

  // Calcular preço por folha automaticamente
  useEffect(() => {
    const preco = parseFloat(formData.preco) || 0;
    const capacidade = parseFloat(formData.capacidade) || 0;
    const precofolha = capacidade > 0 ? preco / capacidade : 0;
    
    setFormData(prev => ({
      ...prev,
      precofolha: precofolha
    }));
  }, [formData.preco, formData.capacidade]);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
    
    // Limpar erro do campo quando usuário digitar
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: ''
      }));
    }
  };

  const validateForm = () => {
    const newErrors = {};
    
    if (!formData.modelo.trim()) {
      newErrors.modelo = 'Modelo é obrigatório';
    }
    
    if (!formData.pesocheio || parseFloat(formData.pesocheio) <= 0) {
      newErrors.pesocheio = 'Peso cheio deve ser maior que zero';
    }
    
    if (!formData.pesovazio || parseFloat(formData.pesovazio) <= 0) {
      newErrors.pesovazio = 'Peso vazio deve ser maior que zero';
    }
    
    if (parseFloat(formData.pesocheio) <= parseFloat(formData.pesovazio)) {
      newErrors.pesocheio = 'Peso cheio deve ser maior que peso vazio';
    }
    
    if (!formData.capacidade || parseFloat(formData.capacidade) <= 0) {
      newErrors.capacidade = 'Capacidade deve ser maior que zero';
    }
    
    if (!formData.preco || parseFloat(formData.preco) <= 0) {
      newErrors.preco = 'Preço deve ser maior que zero';
    }
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    
    if (validateForm()) {
      onSave(formData);
    }
  };

  const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    }).format(value);
  };

  const formatWeight = (value) => {
    return `${value.toFixed(2)}g`;
  };

  return (
    <div className="toner-form-container">
      <div className="form-header">
        <h3>{toner ? 'Editar Toner' : 'Novo Toner'}</h3>
      </div>

      <form className="toner-form" onSubmit={handleSubmit}>
        <div className="form-grid">
          <div className="form-group">
            <label htmlFor="modelo">Modelo *</label>
            <input
              type="text"
              id="modelo"
              name="modelo"
              value={formData.modelo}
              onChange={handleInputChange}
              className={errors.modelo ? 'error' : ''}
              placeholder="Ex: HP CF280A"
            />
            {errors.modelo && <span className="error-message">{errors.modelo}</span>}
          </div>

          <div className="form-group">
            <label htmlFor="pesocheio">Peso Cheio (g) *</label>
            <input
              type="number"
              id="pesocheio"
              name="pesocheio"
              value={formData.pesocheio}
              onChange={handleInputChange}
              className={errors.pesocheio ? 'error' : ''}
              placeholder="0.00"
              step="0.01"
              min="0"
            />
            {errors.pesocheio && <span className="error-message">{errors.pesocheio}</span>}
          </div>

          <div className="form-group">
            <label htmlFor="pesovazio">Peso Vazio (g) *</label>
            <input
              type="number"
              id="pesovazio"
              name="pesovazio"
              value={formData.pesovazio}
              onChange={handleInputChange}
              className={errors.pesovazio ? 'error' : ''}
              placeholder="0.00"
              step="0.01"
              min="0"
            />
            {errors.pesovazio && <span className="error-message">{errors.pesovazio}</span>}
          </div>

          <div className="form-group">
            <label htmlFor="gramatura">Gramatura (g)</label>
            <input
              type="text"
              id="gramatura"
              name="gramatura"
              value={formatWeight(formData.gramatura)}
              readOnly
              className="readonly-field"
            />
            <small className="field-help">Calculado automaticamente (Peso Cheio - Peso Vazio)</small>
          </div>

          <div className="form-group">
            <label htmlFor="capacidade">Capacidade (folhas) *</label>
            <input
              type="number"
              id="capacidade"
              name="capacidade"
              value={formData.capacidade}
              onChange={handleInputChange}
              className={errors.capacidade ? 'error' : ''}
              placeholder="0"
              min="1"
            />
            {errors.capacidade && <span className="error-message">{errors.capacidade}</span>}
          </div>

          <div className="form-group">
            <label htmlFor="gramaturafolha">Gramatura por Folha (g)</label>
            <input
              type="text"
              id="gramaturafolha"
              name="gramaturafolha"
              value={formatWeight(formData.gramaturafolha)}
              readOnly
              className="readonly-field"
            />
            <small className="field-help">Calculado automaticamente (Gramatura ÷ Capacidade)</small>
          </div>

          <div className="form-group">
            <label htmlFor="preco">Preço *</label>
            <input
              type="number"
              id="preco"
              name="preco"
              value={formData.preco}
              onChange={handleInputChange}
              className={errors.preco ? 'error' : ''}
              placeholder="0.00"
              step="0.01"
              min="0"
            />
            {errors.preco && <span className="error-message">{errors.preco}</span>}
          </div>

          <div className="form-group">
            <label htmlFor="precofolha">Preço por Folha</label>
            <input
              type="text"
              id="precofolha"
              name="precofolha"
              value={formatCurrency(formData.precofolha)}
              readOnly
              className="readonly-field"
            />
            <small className="field-help">Calculado automaticamente (Preço ÷ Capacidade)</small>
          </div>

          <div className="form-group">
            <label htmlFor="cor">Cor *</label>
            <select
              id="cor"
              name="cor"
              value={formData.cor}
              onChange={handleInputChange}
            >
              <option value="Black">Black</option>
              <option value="Cyan">Cyan</option>
              <option value="Magenta">Magenta</option>
              <option value="Yellow">Yellow</option>
            </select>
          </div>

          <div className="form-group">
            <label htmlFor="tipo">Tipo *</label>
            <select
              id="tipo"
              name="tipo"
              value={formData.tipo}
              onChange={handleInputChange}
            >
              <option value="Compativel">Compatível</option>
              <option value="Original">Original</option>
              <option value="Remanufaturado">Remanufaturado</option>
            </select>
          </div>
        </div>

        <div className="form-actions">
          <button 
            type="button" 
            className="btn-secondary"
            onClick={onCancel}
          >
            Cancelar
          </button>
          <button type="submit" className="btn-primary">
            {toner ? 'Atualizar' : 'Salvar'}
          </button>
        </div>
      </form>
    </div>
  );
};

export default TonerForm;
