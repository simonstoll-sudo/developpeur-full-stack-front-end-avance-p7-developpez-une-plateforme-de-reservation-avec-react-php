import { useEffect, useState } from 'react';
import CarteLogement from '../components/CarteLogement';
import { getLogements } from '../lib/api';
import type { Logement } from '../lib/types';

export default function Accueil() {
  const [logements, setLogements] = useState<Logement[] | null>(null);
  const [erreur, setErreur] = useState<string | null>(null);

  useEffect(() => {
    let actif = true;

    getLogements()
      .then((resultat) => {
        if (actif) setLogements(resultat);
      })
      .catch((e: unknown) => {
        if (actif) setErreur(e instanceof Error ? e.message : 'Impossible de charger les logements');
      });

    return () => {
      actif = false;
    };
  }, []);

  return (
    <>
      <h1>Nos logements</h1>
      {erreur && (
        <p className="erreur" role="alert">
          {erreur}
        </p>
      )}
      {!erreur && logements === null && <p>Chargement…</p>}
      {logements && (
        <ul className="grille-logements">
          {logements.map((logement) => (
            <CarteLogement key={logement.id} logement={logement} />
          ))}
        </ul>
      )}
    </>
  );
}
