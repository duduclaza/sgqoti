-- Create users table
CREATE TABLE IF NOT EXISTS users (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  email VARCHAR(255) UNIQUE NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  role VARCHAR(50) DEFAULT 'user' CHECK (role IN ('admin', 'user', 'manager')),
  created_by UUID REFERENCES auth.users(id),
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create items table
CREATE TABLE IF NOT EXISTS items (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  category VARCHAR(100),
  quantity INTEGER DEFAULT 0 CHECK (quantity >= 0),
  minimum_stock INTEGER DEFAULT 0 CHECK (minimum_stock >= 0),
  created_by UUID REFERENCES auth.users(id),
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create toners table (specific for the application)
CREATE TABLE IF NOT EXISTS toners (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  modelo VARCHAR(255) NOT NULL,
  cor VARCHAR(50) NOT NULL,
  estoque INTEGER DEFAULT 0 CHECK (estoque >= 0),
  minimo INTEGER DEFAULT 0 CHECK (minimo >= 0),
  status VARCHAR(20) DEFAULT 'OK' CHECK (status IN ('OK', 'Baixo', 'Crítico')),
  created_by UUID REFERENCES auth.users(id),
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create homologacoes table
CREATE TABLE IF NOT EXISTS homologacoes (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  descricao TEXT,
  status VARCHAR(50) DEFAULT 'Pendente' CHECK (status IN ('Pendente', 'Em Análise', 'Aprovado', 'Rejeitado')),
  data_inicio DATE,
  data_fim DATE,
  responsavel VARCHAR(255),
  created_by UUID REFERENCES auth.users(id),
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create amostragens table
CREATE TABLE IF NOT EXISTS amostragens (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  codigo_amostra VARCHAR(100) UNIQUE NOT NULL,
  produto VARCHAR(255) NOT NULL,
  lote VARCHAR(100),
  data_coleta DATE NOT NULL,
  responsavel_coleta VARCHAR(255),
  status VARCHAR(50) DEFAULT 'Coletada' CHECK (status IN ('Coletada', 'Em Análise', 'Aprovada', 'Reprovada')),
  observacoes TEXT,
  created_by UUID REFERENCES auth.users(id),
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create garantias table
CREATE TABLE IF NOT EXISTS garantias (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  produto VARCHAR(255) NOT NULL,
  numero_serie VARCHAR(100),
  data_compra DATE,
  data_vencimento DATE,
  fornecedor VARCHAR(255),
  status VARCHAR(50) DEFAULT 'Ativa' CHECK (status IN ('Ativa', 'Vencida', 'Utilizada')),
  observacoes TEXT,
  created_by UUID REFERENCES auth.users(id),
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Enable Row Level Security (RLS)
ALTER TABLE users ENABLE ROW LEVEL SECURITY;
ALTER TABLE items ENABLE ROW LEVEL SECURITY;
ALTER TABLE toners ENABLE ROW LEVEL SECURITY;
ALTER TABLE homologacoes ENABLE ROW LEVEL SECURITY;
ALTER TABLE amostragens ENABLE ROW LEVEL SECURITY;
ALTER TABLE garantias ENABLE ROW LEVEL SECURITY;

-- Create policies for authenticated users
CREATE POLICY "Users can view all users" ON users FOR SELECT USING (auth.role() = 'authenticated');
CREATE POLICY "Users can insert users" ON users FOR INSERT WITH CHECK (auth.role() = 'authenticated');
CREATE POLICY "Users can update users" ON users FOR UPDATE USING (auth.role() = 'authenticated');
CREATE POLICY "Users can delete users" ON users FOR DELETE USING (auth.role() = 'authenticated');

CREATE POLICY "Users can view all items" ON items FOR SELECT USING (auth.role() = 'authenticated');
CREATE POLICY "Users can insert items" ON items FOR INSERT WITH CHECK (auth.role() = 'authenticated');
CREATE POLICY "Users can update items" ON items FOR UPDATE USING (auth.role() = 'authenticated');
CREATE POLICY "Users can delete items" ON items FOR DELETE USING (auth.role() = 'authenticated');

CREATE POLICY "Users can view all toners" ON toners FOR SELECT USING (auth.role() = 'authenticated');
CREATE POLICY "Users can insert toners" ON toners FOR INSERT WITH CHECK (auth.role() = 'authenticated');
CREATE POLICY "Users can update toners" ON toners FOR UPDATE USING (auth.role() = 'authenticated');
CREATE POLICY "Users can delete toners" ON toners FOR DELETE USING (auth.role() = 'authenticated');

CREATE POLICY "Users can view all homologacoes" ON homologacoes FOR SELECT USING (auth.role() = 'authenticated');
CREATE POLICY "Users can insert homologacoes" ON homologacoes FOR INSERT WITH CHECK (auth.role() = 'authenticated');
CREATE POLICY "Users can update homologacoes" ON homologacoes FOR UPDATE USING (auth.role() = 'authenticated');
CREATE POLICY "Users can delete homologacoes" ON homologacoes FOR DELETE USING (auth.role() = 'authenticated');

CREATE POLICY "Users can view all amostragens" ON amostragens FOR SELECT USING (auth.role() = 'authenticated');
CREATE POLICY "Users can insert amostragens" ON amostragens FOR INSERT WITH CHECK (auth.role() = 'authenticated');
CREATE POLICY "Users can update amostragens" ON amostragens FOR UPDATE USING (auth.role() = 'authenticated');
CREATE POLICY "Users can delete amostragens" ON amostragens FOR DELETE USING (auth.role() = 'authenticated');

CREATE POLICY "Users can view all garantias" ON garantias FOR SELECT USING (auth.role() = 'authenticated');
CREATE POLICY "Users can insert garantias" ON garantias FOR INSERT WITH CHECK (auth.role() = 'authenticated');
CREATE POLICY "Users can update garantias" ON garantias FOR UPDATE USING (auth.role() = 'authenticated');
CREATE POLICY "Users can delete garantias" ON garantias FOR DELETE USING (auth.role() = 'authenticated');
