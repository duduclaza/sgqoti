import React from 'react'
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider } from './hooks/useAuth.jsx'
import Layout from './components/Layout'
import ProtectedRoute from './components/ProtectedRoute'
import Login from './pages/Login'
import Register from './pages/Register'
import Dashboard from './pages/Dashboard'
import ControleToners from './pages/ControleToners'

// Páginas placeholder para as outras rotas
const Homologacoes = () => <div className="p-6"><h1 className="text-2xl font-bold">Homologações</h1><p>Página em desenvolvimento...</p></div>
const Amostragens = () => <div className="p-6"><h1 className="text-2xl font-bold">Amostragens</h1><p>Página em desenvolvimento...</p></div>
const Garantias = () => <div className="p-6"><h1 className="text-2xl font-bold">Garantias</h1><p>Página em desenvolvimento...</p></div>
const ControleDescartes = () => <div className="p-6"><h1 className="text-2xl font-bold">Controle de Descartes</h1><p>Página em desenvolvimento...</p></div>
const FEMEA = () => <div className="p-6"><h1 className="text-2xl font-bold">FEMEA</h1><p>Página em desenvolvimento...</p></div>
const POPsITs = () => <div className="p-6"><h1 className="text-2xl font-bold">POPs e ITs</h1><p>Página em desenvolvimento...</p></div>
const Fluxogramas = () => <div className="p-6"><h1 className="text-2xl font-bold">Fluxogramas</h1><p>Página em desenvolvimento...</p></div>
const MelhoriaContinua = () => <div className="p-6"><h1 className="text-2xl font-bold">Melhoria Contínua</h1><p>Página em desenvolvimento...</p></div>
const ControleRC = () => <div className="p-6"><h1 className="text-2xl font-bold">Controle de RC</h1><p>Página em desenvolvimento...</p></div>
const Configuracoes = () => <div className="p-6"><h1 className="text-2xl font-bold">Configurações</h1><p>Página em desenvolvimento...</p></div>

function App() {
  return (
    <AuthProvider>
      <Router>
        <Routes>
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route
            path="/"
            element={
              <ProtectedRoute>
                <Layout />
              </ProtectedRoute>
            }
          >
            <Route index element={<Navigate to="/dashboard" replace />} />
            <Route path="dashboard" element={<Dashboard />} />
            <Route path="controle-toners" element={<ControleToners />} />
            <Route path="homologacoes" element={<Homologacoes />} />
            <Route path="amostragens" element={<Amostragens />} />
            <Route path="garantias" element={<Garantias />} />
            <Route path="controle-descartes" element={<ControleDescartes />} />
            <Route path="femea" element={<FEMEA />} />
            <Route path="pops-its" element={<POPsITs />} />
            <Route path="fluxogramas" element={<Fluxogramas />} />
            <Route path="melhoria-continua" element={<MelhoriaContinua />} />
            <Route path="controle-rc" element={<ControleRC />} />
            <Route path="configuracoes" element={<Configuracoes />} />
          </Route>
        </Routes>
      </Router>
    </AuthProvider>
  )
}

export default App
