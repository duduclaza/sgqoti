import bcrypt from 'bcryptjs'
import { query } from '../config/database.js'
import { generateToken } from '../middleware/auth.js'

export const login = async (req, res) => {
  try {
    const { email, password } = req.body

    // Buscar usuário no banco
    const users = await query('SELECT * FROM users WHERE email = ?', [email])
    
    if (users.length === 0) {
      return res.status(401).json({ error: 'Email ou senha inválidos' })
    }

    const user = users[0]

    // Verificar senha
    const isValidPassword = await bcrypt.compare(password, user.password_hash)
    
    if (!isValidPassword) {
      return res.status(401).json({ error: 'Email ou senha inválidos' })
    }

    const token = generateToken(user.id)

    res.json({
      message: 'Login realizado com sucesso',
      token,
      user: {
        id: user.id,
        email: user.email,
        fullName: user.full_name,
        role: user.role
      }
    })
  } catch (error) {
    console.error('Erro no login:', error)
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}

export const register = async (req, res) => {
  try {
    const { email, password, fullName } = req.body

    // Verificar se usuário já existe
    const existingUsers = await query('SELECT id FROM users WHERE email = ?', [email])
    
    if (existingUsers.length > 0) {
      return res.status(400).json({ error: 'Email já está em uso' })
    }

    // Hash da senha
    const saltRounds = 10
    const passwordHash = await bcrypt.hash(password, saltRounds)

    // Inserir usuário
    const result = await query(
      'INSERT INTO users (id, email, password_hash, full_name, role) VALUES (UUID(), ?, ?, ?, ?)',
      [email, passwordHash, fullName, 'user']
    )

    // Buscar usuário criado
    const newUsers = await query('SELECT * FROM users WHERE email = ?', [email])
    const newUser = newUsers[0]

    res.status(201).json({
      message: 'Usuário registrado com sucesso',
      user: {
        id: newUser.id,
        email: newUser.email,
        fullName: newUser.full_name,
        role: newUser.role
      }
    })
  } catch (error) {
    console.error('Erro no registro:', error)
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}

export const logout = async (req, res) => {
  try {
    res.json({ message: 'Logout realizado com sucesso' })
  } catch (error) {
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}

export const getProfile = async (req, res) => {
  try {
    const users = await query('SELECT * FROM users WHERE id = ?', [req.user.id])
    
    if (users.length === 0) {
      return res.status(404).json({ error: 'Usuário não encontrado' })
    }

    const user = users[0]

    res.json({
      user: {
        id: user.id,
        email: user.email,
        fullName: user.full_name,
        role: user.role
      }
    })
  } catch (error) {
    console.error('Erro ao buscar perfil:', error)
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}
