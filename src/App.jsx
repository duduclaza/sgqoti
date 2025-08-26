import { useState } from 'react'
import { ThemeProvider } from './contexts/ThemeContext'
import Header from './components/Header'
import Sidebar from './components/Sidebar'
import MainContent from './components/MainContent'
import './App.css'

function App() {
  const [activeModule, setActiveModule] = useState('toners')

  const modules = [
    { id: 'toners', name: 'Controle de Toners', icon: '🖨️' },
    { id: 'homologacoes', name: 'Homologações', icon: '✅' },
    { id: 'amostragens', name: 'Amostragens', icon: '🧪' },
    { id: 'garantias', name: 'Garantias', icon: '🛡️' },
    { id: 'pops-its', name: 'POPs e ITs', icon: '📋' },
    { id: 'fluxogramas', name: 'Fluxogramas', icon: '📊' },
    { id: 'auditorias', name: 'Auditorias', icon: '🔍' },
    { id: 'dinamicas', name: 'Dinâmicas', icon: '⚡' },
    { id: 'configuracoes', name: 'Configurações', icon: '⚙️' }
  ]

  return (
    <ThemeProvider>
      <div className="App">
        <Header activeModule={activeModule} modules={modules} />
        <Sidebar activeModule={activeModule} setActiveModule={setActiveModule} modules={modules} />
        <MainContent activeModule={activeModule} />
      </div>
    </ThemeProvider>
  )
}

export default App
