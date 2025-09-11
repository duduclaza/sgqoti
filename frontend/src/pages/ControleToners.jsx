import React, { useState, useEffect } from 'react'
import { Plus, Upload, Download, Edit, Trash2 } from 'lucide-react'
import tonersService from '../services/tonersService'

const ControleToners = () => {
  const [activeTab, setActiveTab] = useState('cadastro')
  const [toners, setToners] = useState([])
  const [loading, setLoading] = useState(false)
  const [showModal, setShowModal] = useState(false)
  const [showImportModal, setShowImportModal] = useState(false)
  const [editingToner, setEditingToner] = useState(null)
  const [formData, setFormData] = useState({
    modelo: '',
    peso_cheio: '',
    peso_vazio: '',
    capacidade_folhas: '',
    preco_toner: '',
    cor: 'Black',
    tipo: 'Original'
  })

  // Cálculos automáticos
  const gramatura = formData.peso_cheio && formData.peso_vazio ? 
    (parseFloat(formData.peso_cheio) - parseFloat(formData.peso_vazio)).toFixed(3) : '0.000'
  
  const gramaturaPerFolha = gramatura && formData.capacidade_folhas ? 
    (parseFloat(gramatura) / parseFloat(formData.capacidade_folhas)).toFixed(6) : '0.000000'
  
  const custoPerFolha = formData.preco_toner && formData.capacidade_folhas ? 
    (parseFloat(formData.preco_toner) / parseFloat(formData.capacidade_folhas)).toFixed(6) : '0.000000'

  const handleInputChange = (e) => {
    const { name, value } = e.target
    setFormData(prev => ({
      ...prev,
      [name]: value
    }))
  }

  // Carregar toners ao montar o componente
  useEffect(() => {
    loadToners()
  }, [])

  const loadToners = async () => {
    try {
      setLoading(true)
      const data = await tonersService.getAllToners()
      setToners(data)
    } catch (error) {
      console.error('Erro ao carregar toners:', error)
      alert('Erro ao carregar toners: ' + error.message)
    } finally {
      setLoading(false)
    }
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    try {
      setLoading(true)
      
      if (editingToner) {
        await tonersService.updateToner(editingToner.id, formData)
        alert('Toner atualizado com sucesso!')
      } else {
        await tonersService.createToner(formData)
        alert('Toner cadastrado com sucesso!')
      }
      
      setShowModal(false)
      resetForm()
      await loadToners() // Recarregar lista
    } catch (error) {
      console.error('Erro ao salvar toner:', error)
      alert('Erro ao salvar toner: ' + error.message)
    } finally {
      setLoading(false)
    }
  }

  const handleEdit = async (toner) => {
    try {
      const tonerData = await tonersService.getTonerById(toner.id)
      setFormData({
        modelo: tonerData.modelo,
        peso_cheio: tonerData.peso_cheio,
        peso_vazio: tonerData.peso_vazio,
        capacidade_folhas: tonerData.capacidade_folhas,
        preco_toner: tonerData.preco_toner,
        cor: tonerData.cor,
        tipo: tonerData.tipo
      })
      setEditingToner(tonerData)
      setShowModal(true)
    } catch (error) {
      console.error('Erro ao carregar toner:', error)
      alert('Erro ao carregar toner: ' + error.message)
    }
  }

  const handleDelete = async (id) => {
    if (window.confirm('Tem certeza que deseja excluir este toner?')) {
      try {
        setLoading(true)
        await tonersService.deleteToner(id)
        alert('Toner excluído com sucesso!')
        await loadToners() // Recarregar lista
      } catch (error) {
        console.error('Erro ao excluir toner:', error)
        alert('Erro ao excluir toner: ' + error.message)
      } finally {
        setLoading(false)
      }
    }
  }

  const resetForm = () => {
    setFormData({
      modelo: '',
      peso_cheio: '',
      peso_vazio: '',
      capacidade_folhas: '',
      preco_toner: '',
      cor: 'Black',
      tipo: 'Original'
    })
    setEditingToner(null)
  }

  const tabs = [
    { id: 'cadastro', label: 'Cadastro de Toners' },
    { id: 'retornados', label: 'Registro de Retornados' },
    { id: 'graficos', label: 'Gráficos de Retornados' }
  ]

  return (
    <div className="p-6">
      <div className="mb-6">
        <h1 className="text-2xl font-bold text-gray-900 mb-2">Controle de Toners</h1>
        <p className="text-gray-600">Gerencie o cadastro, retornos e análises de toners</p>
      </div>

      {/* Tabs */}
      <div className="mb-6">
        <div className="border-b border-gray-200">
          <nav className="-mb-px flex space-x-8">
            {tabs.map((tab) => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`py-2 px-1 border-b-2 font-medium text-sm ${
                  activeTab === tab.id
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                }`}
              >
                {tab.label}
              </button>
            ))}
          </nav>
        </div>
      </div>

      {/* Cadastro de Toners Tab */}
      {activeTab === 'cadastro' && (
        <div className="bg-white rounded-lg shadow">
          <div className="p-6 border-b border-gray-200">
            <div className="flex justify-between items-center">
              <h2 className="text-lg font-medium text-gray-900">Cadastro de Toners</h2>
              <div className="flex space-x-3">
                <button
                  onClick={() => setShowImportModal(true)}
                  className="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  <Upload className="w-4 h-4 mr-2" />
                  Importar
                </button>
                <button
                  onClick={() => setShowModal(true)}
                  className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                >
                  <Plus className="w-4 h-4 mr-2" />
                  Novo Toner
                </button>
              </div>
            </div>
          </div>

          <div className="p-6">
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peso Cheio</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peso Vazio</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gramatura</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacidade</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cor</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {loading ? (
                    <tr>
                      <td colSpan="9" className="px-6 py-4 text-center text-gray-500">
                        Carregando...
                      </td>
                    </tr>
                  ) : toners.length === 0 ? (
                    <tr>
                      <td colSpan="9" className="px-6 py-4 text-center text-gray-500">
                        Nenhum toner cadastrado
                      </td>
                    </tr>
                  ) : (
                    toners.map((toner) => (
                      <tr key={toner.id}>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                          {toner.modelo}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {toner.peso_cheio}g
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {toner.peso_vazio}g
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {toner.gramatura}g
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {toner.capacidade_folhas}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          R$ {parseFloat(toner.preco_toner).toFixed(2)}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {toner.cor}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {toner.tipo}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                          <button 
                            onClick={() => handleEdit(toner)}
                            className="text-blue-600 hover:text-blue-900 mr-3"
                            disabled={loading}
                          >
                            <Edit className="w-4 h-4" />
                          </button>
                          <button 
                            onClick={() => handleDelete(toner.id)}
                            className="text-red-600 hover:text-red-900"
                            disabled={loading}
                          >
                            <Trash2 className="w-4 h-4" />
                          </button>
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* Registro de Retornados Tab */}
      {activeTab === 'retornados' && (
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-lg font-medium text-gray-900 mb-4">Registro de Retornados</h2>
          <p className="text-gray-500">Funcionalidade em desenvolvimento...</p>
        </div>
      )}

      {/* Gráficos de Retornados Tab */}
      {activeTab === 'graficos' && (
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-lg font-medium text-gray-900 mb-4">Gráficos de Retornados</h2>
          <p className="text-gray-500">Funcionalidade em desenvolvimento...</p>
        </div>
      )}

      {/* Modal de Cadastro/Edição */}
      {showModal && (
        <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
          <div className="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
            <div className="mt-3">
              <h3 className="text-lg font-medium text-gray-900 mb-4">
                {editingToner ? 'Editar Toner' : 'Novo Toner'}
              </h3>
              
              <form onSubmit={handleSubmit} className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                    <input
                      type="text"
                      name="modelo"
                      value={formData.modelo}
                      onChange={handleInputChange}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      required
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Peso Cheio (g)</label>
                    <input
                      type="number"
                      step="0.001"
                      name="peso_cheio"
                      value={formData.peso_cheio}
                      onChange={handleInputChange}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      required
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Peso Vazio (g)</label>
                    <input
                      type="number"
                      step="0.001"
                      name="peso_vazio"
                      value={formData.peso_vazio}
                      onChange={handleInputChange}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      required
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Gramatura (calculado)</label>
                    <input
                      type="text"
                      value={gramatura + 'g'}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50"
                      disabled
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Capacidade de Folhas</label>
                    <input
                      type="number"
                      name="capacidade_folhas"
                      value={formData.capacidade_folhas}
                      onChange={handleInputChange}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      required
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Preço do Toner (R$)</label>
                    <input
                      type="number"
                      step="0.01"
                      name="preco_toner"
                      value={formData.preco_toner}
                      onChange={handleInputChange}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      required
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Gramatura por Folha (calculado)</label>
                    <input
                      type="text"
                      value={gramaturaPerFolha + 'g'}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50"
                      disabled
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Custo por Folha (calculado)</label>
                    <input
                      type="text"
                      value={'R$ ' + custoPerFolha}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50"
                      disabled
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Cor</label>
                    <select
                      name="cor"
                      value={formData.cor}
                      onChange={handleInputChange}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="Black">Black</option>
                      <option value="Yellow">Yellow</option>
                      <option value="Magenta">Magenta</option>
                      <option value="Cyan">Cyan</option>
                    </select>
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <select
                      name="tipo"
                      value={formData.tipo}
                      onChange={handleInputChange}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="Original">Original</option>
                      <option value="Compativel">Compatível</option>
                      <option value="Remanufaturado">Remanufaturado</option>
                    </select>
                  </div>
                </div>
                
                <div className="flex justify-end space-x-3 pt-4">
                  <button
                    type="button"
                    onClick={() => {
                      setShowModal(false)
                      resetForm()
                    }}
                    className="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    className="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                  >
                    {editingToner ? 'Atualizar' : 'Salvar'}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}

      {/* Modal de Importação */}
      {showImportModal && (
        <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
          <div className="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div className="mt-3">
              <h3 className="text-lg font-medium text-gray-900 mb-4">Importar Toners</h3>
              
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Selecionar arquivo Excel
                  </label>
                  <input
                    type="file"
                    accept=".xlsx,.xls"
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                
                <div className="border-t pt-4">
                  <button
                    onClick={() => {
                      // Implementar download do template
                      console.log('Download template')
                    }}
                    className="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                  >
                    <Download className="w-4 h-4 mr-2" />
                    Baixar Planilha Template
                  </button>
                </div>
              </div>
              
              <div className="flex justify-end space-x-3 pt-6">
                <button
                  onClick={() => setShowImportModal(false)}
                  className="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                  Cancelar
                </button>
                <button
                  onClick={() => {
                    // Implementar importação
                    console.log('Importar dados')
                    setShowImportModal(false)
                  }}
                  className="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                >
                  Importar
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}

export default ControleToners
