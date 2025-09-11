import { query } from '../config/database.js'

export const getAllUsers = async (req, res) => {
  try {
    const users = await query('SELECT id, email, full_name, role, created_at, updated_at FROM users ORDER BY created_at DESC')
    res.json({ users })
  } catch (error) {
    console.error('Erro ao buscar usuários:', error)
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}

export const getUserById = async (req, res) => {
  try {
    const { id } = req.params
    const users = await query('SELECT id, email, full_name, role, created_at, updated_at FROM users WHERE id = ?', [id])

    if (users.length === 0) {
      return res.status(404).json({ error: 'Usuário não encontrado' })
    }

    res.json({ user: users[0] })
  } catch (error) {
    console.error('Erro ao buscar usuário:', error)
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}

export const createUser = async (req, res) => {
  try {
    const { email, fullName, role = 'user' } = req.body

    await query(
      'INSERT INTO users (id, email, full_name, role, created_by) VALUES (UUID(), ?, ?, ?, ?)',
      [email, fullName, role, req.user.id]
    )

    const users = await query('SELECT id, email, full_name, role, created_at FROM users WHERE email = ?', [email])
    res.status(201).json({ user: users[0] })
  } catch (error) {
    console.error('Erro ao criar usuário:', error)
    if (error.code === 'ER_DUP_ENTRY') {
      return res.status(400).json({ error: 'Email já está em uso' })
    }
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}

export const updateUser = async (req, res) => {
  try {
    const { id } = req.params
    const { email, fullName, role } = req.body

    const updateFields = []
    const updateValues = []

    if (email) {
      updateFields.push('email = ?')
      updateValues.push(email)
    }
    if (fullName) {
      updateFields.push('full_name = ?')
      updateValues.push(fullName)
    }
    if (role) {
      updateFields.push('role = ?')
      updateValues.push(role)
    }

    if (updateFields.length === 0) {
      return res.status(400).json({ error: 'Nenhum campo para atualizar' })
    }

    updateValues.push(id)

    await query(
      `UPDATE users SET ${updateFields.join(', ')}, updated_at = CURRENT_TIMESTAMP WHERE id = ?`,
      updateValues
    )

    const users = await query('SELECT id, email, full_name, role, created_at, updated_at FROM users WHERE id = ?', [id])
    
    if (users.length === 0) {
      return res.status(404).json({ error: 'Usuário não encontrado' })
    }

    res.json({ user: users[0] })
  } catch (error) {
    console.error('Erro ao atualizar usuário:', error)
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}

export const deleteUser = async (req, res) => {
  try {
    const { id } = req.params

    const result = await query('DELETE FROM users WHERE id = ?', [id])

    if (result.affectedRows === 0) {
      return res.status(404).json({ error: 'Usuário não encontrado' })
    }

    res.json({ message: 'Usuário deletado com sucesso' })
  } catch (error) {
    console.error('Erro ao deletar usuário:', error)
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}
