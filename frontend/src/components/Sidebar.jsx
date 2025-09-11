import React from 'react'
import { NavLink } from 'react-router-dom'
import {
  Printer,
  CheckCircle,
  TestTube,
  Shield,
  Trash2,
  AlertTriangle,
  FileText,
  GitBranch,
  TrendingUp,
  Settings,
  BarChart3
} from 'lucide-react'

const menuItems = [
  {
    path: '/controle-toners',
    label: 'Controle de Toners',
    icon: Printer
  },
  {
    path: '/homologacoes',
    label: 'Homologações',
    icon: CheckCircle
  },
  {
    path: '/amostragens',
    label: 'Amostragens',
    icon: TestTube
  },
  {
    path: '/garantias',
    label: 'Garantias',
    icon: Shield
  },
  {
    path: '/controle-descartes',
    label: 'Controle de Descartes',
    icon: Trash2
  },
  {
    path: '/femea',
    label: 'FEMEA',
    icon: AlertTriangle
  },
  {
    path: '/pops-its',
    label: 'POPs e ITs',
    icon: FileText
  },
  {
    path: '/fluxogramas',
    label: 'Fluxogramas',
    icon: GitBranch
  },
  {
    path: '/melhoria-continua',
    label: 'Melhoria Contínua',
    icon: TrendingUp
  },
  {
    path: '/controle-rc',
    label: 'Controle de RC',
    icon: BarChart3
  },
  {
    path: '/configuracoes',
    label: 'Configurações',
    icon: Settings
  }
]

const Sidebar = () => {
  return (
    <div className="w-64 bg-sidebar-bg h-screen fixed left-0 top-0 overflow-y-auto">
      <div className="border-b border-gray-700">
        <div className="flex flex-col items-center text-center">
          <img 
            src="/src/assets/logo.png" 
            alt="Logo SGQ OTI" 
            className="w-28 h-28 object-contain -mb-2"
          />
          <div className="px-4 pb-1">
            <h1 className="text-white text-lg font-bold mb-0">
              SGQ OTI
            </h1>
            <p className="text-gray-300 text-sm leading-tight">
              Sistema de Gestão da Qualidade
            </p>
          </div>
        </div>
      </div>
      
      <nav className="mt-8">
        {menuItems.map((item) => {
          const IconComponent = item.icon
          return (
            <NavLink
              key={item.path}
              to={item.path}
              className={({ isActive }) =>
                `sidebar-item ${isActive ? 'active' : ''}`
              }
            >
              <IconComponent />
              <span>{item.label}</span>
            </NavLink>
          )
        })}
      </nav>
    </div>
  )
}

export default Sidebar
