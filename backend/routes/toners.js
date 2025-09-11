import express from 'express';
import { 
  getToners, 
  getTonerById, 
  createToner, 
  updateToner, 
  deleteToner 
} from '../controllers/tonersController.js';

const router = express.Router();

// GET /api/toners - Listar todos os toners
router.get('/', getToners);

// GET /api/toners/:id - Buscar toner por ID
router.get('/:id', getTonerById);

// POST /api/toners - Criar novo toner
router.post('/', createToner);

// PUT /api/toners/:id - Atualizar toner
router.put('/:id', updateToner);

// DELETE /api/toners/:id - Excluir toner
router.delete('/:id', deleteToner);

export default router;
