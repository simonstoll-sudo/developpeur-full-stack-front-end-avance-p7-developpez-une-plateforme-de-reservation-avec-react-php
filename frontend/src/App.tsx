import { BrowserRouter, Route, Routes } from 'react-router';
import Header from './components/Header';
import Accueil from './pages/Accueil';
import Connexion from './pages/Connexion';
import DetailLogement from './pages/DetailLogement';

export default function App() {
  return (
    <BrowserRouter>
      <Header />
      <main className="conteneur">
        <Routes>
          <Route path="/" element={<Accueil />} />
          <Route path="/logements/:id" element={<DetailLogement />} />
          <Route path="/connexion" element={<Connexion />} />
        </Routes>
      </main>
    </BrowserRouter>
  );
}
