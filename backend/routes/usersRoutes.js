import express from 'express'
import { 
  getAllUsers, 
  getUserById, 
  createUser, 
  updateUser, 
  deleteUser 
} from '../controllers/usersController.js'
import { authenticateToken } from '../middleware/auth.js'
import { validateUser } from '../middleware/validation.js'

const router = express.Router()

// All routes are protected
router.use(authenticateToken)

router.get('/', getAllUsers)
router.get('/:id', getUserById)
router.post('/', validateUser, createUser)
router.put('/:id', validateUser, updateUser)
router.delete('/:id', deleteUser)

export default router
