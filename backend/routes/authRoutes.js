import express from 'express'
import { login, register, logout, getProfile } from '../controllers/authController.js'
import { authenticateToken } from '../middleware/auth.js'
import { validateLogin, validateRegister } from '../middleware/validation.js'

const router = express.Router()

// Public routes
router.post('/login', validateLogin, login)
router.post('/register', validateRegister, register)

// Protected routes
router.post('/logout', authenticateToken, logout)
router.get('/profile', authenticateToken, getProfile)

export default router
