import { query } from '../config/database.js'

export const getAllItems = async (req, res) => {
  try {
    const items = await query('SELECT * FROM items ORDER BY created_at DESC')
    res.json({ items })
  } catch (error) {
    console.error('Erro ao buscar items:', error)
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}

export const getItemById = async (req, res) => {
  try {
    const { id } = req.params
    const items = await query('SELECT * FROM items WHERE id = ?', [id])

    if (items.length === 0) {
      return res.status(404).json({ error: 'Item não encontrado' })
    }

    res.json({ item: items[0] })
  } catch (error) {
    console.error('Erro ao buscar item:', error)
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}

export const createItem = async (req, res) => {
  try {
    const { name, description, category, quantity = 0, minimum_stock = 0 } = req.body

    await query(
      'INSERT INTO items (id, name, description, category, quantity, minimum_stock, created_by) VALUES (UUID(), ?, ?, ?, ?, ?, ?)',
      [name, description, category, quantity, minimum_stock, req.user.id]
    )

    const items = await query('SELECT * FROM items WHERE name = ? AND created_by = ? ORDER BY created_at DESC LIMIT 1', [name, req.user.id])
    res.status(201).json({ item: items[0] })
  } catch (error) {
    console.error('Erro ao criar item:', error)
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}

export const updateItem = async (req, res) => {
  try {
    const { id } = req.params
    const { name, description, category, quantity, minimum_stock } = req.body

    const updateFields = []
    const updateValues = []

    if (name) {
      updateFields.push('name = ?')
      updateValues.push(name)
    }
    if (description !== undefined) {
      updateFields.push('description = ?')
      updateValues.push(description)
    }
    if (category) {
      updateFields.push('category = ?')
      updateValues.push(category)
    }
    if (quantity !== undefined) {
      updateFields.push('quantity = ?')
      updateValues.push(quantity)
    }
    if (minimum_stock !== undefined) {
      updateFields.push('minimum_stock = ?')
      updateValues.push(minimum_stock)
    }

    if (updateFields.length === 0) {
      return res.status(400).json({ error: 'Nenhum campo para atualizar' })
    }

    updateValues.push(id)

    await query(
      `UPDATE items SET ${updateFields.join(', ')}, updated_at = CURRENT_TIMESTAMP WHERE id = ?`,
      updateValues
    )

    const items = await query('SELECT * FROM items WHERE id = ?', [id])
    
    if (items.length === 0) {
      return res.status(404).json({ error: 'Item não encontrado' })
    }

    res.json({ item: items[0] })
  } catch (error) {
    console.error('Erro ao atualizar item:', error)
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}

export const deleteItem = async (req, res) => {
  try {
    const { id } = req.params

    const result = await query('DELETE FROM items WHERE id = ?', [id])

    if (result.affectedRows === 0) {
      return res.status(404).json({ error: 'Item não encontrado' })
    }

    res.json({ message: 'Item deletado com sucesso' })
  } catch (error) {
    console.error('Erro ao deletar item:', error)
    res.status(500).json({ error: 'Erro interno do servidor' })
  }
}
