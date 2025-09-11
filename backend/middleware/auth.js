import jwt from 'jsonwebtoken'
import { query } from '../config/database.js'

export const authenticateToken = async (req, res, next) => {
  const authHeader = req.headers['authorization']
  const token = authHeader && authHeader.split(' ')[1]

  if (!token) {
    return res.status(401).json({ error: 'Token de acesso obrigatório' })
  }

  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET)
    
    // Verificar se o usuário ainda existe no banco
    const users = await query('SELECT * FROM users WHERE id = ?', [decoded.userId])
    
    if (users.length === 0) {
      return res.status(401).json({ error: 'Token inválido' })
    }

    req.user = users[0]
    next()
  } catch (error) {
    return res.status(403).json({ error: 'Token inválido ou expirado' })
  }
}

export const generateToken = (userId) => {
  return jwt.sign(
    { userId },
    process.env.JWT_SECRET,
    { expiresIn: process.env.JWT_EXPIRES_IN }
  )
}

export default { authenticateToken, generateToken }
