import type { Logement, ReponseConnexion } from './types';

const API_URL = import.meta.env.VITE_API_URL ?? 'http://localhost:8000';

export async function getLogements(): Promise<Logement[]> {
  const res = await fetch(`${API_URL}/api/logements`);
  if (!res.ok) {
    throw new Error('Impossible de charger les logements');
  }
  return res.json();
}

export async function getLogement(id: string): Promise<Logement> {
  const res = await fetch(`${API_URL}/api/logements/${id}`);
  if (!res.ok) {
    throw new Error('Impossible de charger les logements');
  }
  return res.json();
}

export async function connexion(email: string, motDePasse: string): Promise<ReponseConnexion> {
  const res = await fetch(`${API_URL}/api/auth/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, motDePasse }),
  });
  if (res.status === 401) {
    throw new Error('Identifiants invalides');
  }
  if (!res.ok) {
    throw new Error('Connexion impossible');
  }
  return res.json();
}
