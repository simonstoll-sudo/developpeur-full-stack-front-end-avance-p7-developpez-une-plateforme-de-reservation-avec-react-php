import { Link } from 'react-router';
import type { Logement } from '../lib/types';

interface CarteLogementProps {
  logement: Logement;
}

export default function CarteLogement({ logement }: CarteLogementProps) {
  return (
    <li className="carte-logement">
      <h2>
        <Link to={`/logements/${logement.id}`}>{logement.titre}</Link>
      </h2>
      <p>
        {logement.ville} · {logement.typeLogement}
      </p>
      <p className="prix">{logement.prixParNuit} € / nuit</p>
    </li>
  );
}
