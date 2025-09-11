import { useState, useEffect, createContext, useContext } from 'react'
import { authAPI } from '../services/api.js'

const AuthContext = createContext({})

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    // Verificar se há token salvo
    const token = localStorage.getItem('token')
    if (token) {
      // Verificar se o token é válido
      authAPI.getProfile()
        .then(response => {
          setUser(response.data.user)
        })
        .catch(() => {
          localStorage.removeItem('token')
        })
        .finally(() => {
          setLoading(false)
        })
    } else {
      setLoading(false)
    }
  }, [])

  const signIn = async (email, password) => {
    try {
      const response = await authAPI.login({ email, password })
      const { token, user } = response.data
      
      localStorage.setItem('token', token)
      setUser(user)
      
      return { data: response.data, error: null }
    } catch (error) {
      return { data: null, error: { message: error.response?.data?.error || 'Erro no login' } }
    }
  }

  const signUp = async (email, password, userData = {}) => {
    try {
      const response = await authAPI.register({ 
        email, 
        password, 
        fullName: userData.full_name || userData.fullName 
      })
      
      return { data: response.data, error: null }
    } catch (error) {
      return { data: null, error: { message: error.response?.data?.error || 'Erro no registro' } }
    }
  }

  const signOut = async () => {
    try {
      await authAPI.logout()
      localStorage.removeItem('token')
      setUser(null)
      return { error: null }
    } catch (error) {
      localStorage.removeItem('token')
      setUser(null)
      return { error: null }
    }
  }

  const value = {
    user,
    loading,
    signIn,
    signUp,
    signOut,
  }

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  )
}

export const useAuth = () => {
  const context = useContext(AuthContext)
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider')
  }
  return context
}
