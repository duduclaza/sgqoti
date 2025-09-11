import express from 'express'
import { 
  getAllItems, 
  getItemById, 
  createItem, 
  updateItem, 
  deleteItem 
} from '../controllers/itemsController.js'
import { authenticateToken } from '../middleware/auth.js'
import { validateItem } from '../middleware/validation.js'

const router = express.Router()

// All routes are protected
router.use(authenticateToken)

router.get('/', getAllItems)
router.get('/:id', getItemById)
router.post('/', validateItem, createItem)
router.put('/:id', validateItem, updateItem)
router.delete('/:id', deleteItem)

export default router
