import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router';
import { getLogement } from '../lib/api';
import type { Logement } from '../lib/types';

export default function DetailLogement() {
  const { id } = useParams<{ id: string }>();
  const [logement, setLogement] = useState<Logement | null>(null);
  const [erreur, setErreur] = useState<string | null>(null);

  useEffect(() => {
    if (id === undefined) return;
    let actif = true;

    getLogement(id)
      .then((resultat) => {
        if (actif) setLogement(resultat);
      })
      .catch((e: unknown) => {
        if (actif) setErreur(e instanceof Error ? e.message : 'Impossible de charger les logements');
      });

    return () => {
      actif = false;
    };
  }, [id]);

  if (erreur) {
    return (
      <p className="erreur" role="alert">
        {erreur}
      </p>
    );
  }

  if (logement === null) {
    return <p>Chargement…</p>;
  }

  return (
    <article className="detail-logement">
      <Link to="/" className="lien-retour">
        ← Retour aux logements
      </Link>
      <h1>{logement.titre}</h1>
      <p className="detail-meta">
        {logement.typeLogement} · {logement.ville}
      </p>
      <p className="prix">{logement.prixParNuit} € / nuit</p>
      {logement.hote && <p>Proposé par {logement.hote.prenom}</p>}
    </article>
  );
}
