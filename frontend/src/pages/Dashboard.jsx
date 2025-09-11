import React from 'react'
import { BarChart3, TrendingUp, Users, AlertCircle } from 'lucide-react'

const Dashboard = () => {
  const stats = [
    {
      title: 'Total de Toners',
      value: '156',
      change: '+12%',
      icon: BarChart3,
      color: 'bg-blue-500'
    },
    {
      title: 'Homologações Ativas',
      value: '23',
      change: '+5%',
      icon: TrendingUp,
      color: 'bg-green-500'
    },
    {
      title: 'Usuários Ativos',
      value: '45',
      change: '+8%',
      icon: Users,
      color: 'bg-purple-500'
    },
    {
      title: 'Alertas Pendentes',
      value: '7',
      change: '-2%',
      icon: AlertCircle,
      color: 'bg-red-500'
    }
  ]

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p className="text-gray-600 mt-2">Visão geral do sistema de gestão de qualidade</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat, index) => {
          const IconComponent = stat.icon
          return (
            <div key={index} className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">{stat.title}</p>
                  <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
                  <p className={`text-sm ${
                    stat.change.startsWith('+') ? 'text-green-600' : 'text-red-600'
                  }`}>
                    {stat.change} vs mês anterior
                  </p>
                </div>
                <div className={`${stat.color} p-3 rounded-full`}>
                  <IconComponent className="w-6 h-6 text-white" />
                </div>
              </div>
            </div>
          )
        })}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-lg shadow-md p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Atividades Recentes</h3>
          <div className="space-y-3">
            <div className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
              <div className="w-2 h-2 bg-blue-500 rounded-full"></div>
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-900">Novo toner HP CF280A adicionado</p>
                <p className="text-xs text-gray-500">2 horas atrás</p>
              </div>
            </div>
            <div className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
              <div className="w-2 h-2 bg-green-500 rounded-full"></div>
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-900">Homologação aprovada</p>
                <p className="text-xs text-gray-500">4 horas atrás</p>
              </div>
            </div>
            <div className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
              <div className="w-2 h-2 bg-yellow-500 rounded-full"></div>
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-900">Amostragem pendente</p>
                <p className="text-xs text-gray-500">6 horas atrás</p>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-md p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Alertas do Sistema</h3>
          <div className="space-y-3">
            <div className="flex items-center space-x-3 p-3 bg-red-50 border border-red-200 rounded-lg">
              <AlertCircle className="w-5 h-5 text-red-500" />
              <div className="flex-1">
                <p className="text-sm font-medium text-red-900">Estoque baixo: Canon CRG-045</p>
                <p className="text-xs text-red-600">Apenas 3 unidades restantes</p>
              </div>
            </div>
            <div className="flex items-center space-x-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
              <AlertCircle className="w-5 h-5 text-yellow-500" />
              <div className="flex-1">
                <p className="text-sm font-medium text-yellow-900">Homologação vencendo</p>
                <p className="text-xs text-yellow-600">Vence em 5 dias</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Dashboard
