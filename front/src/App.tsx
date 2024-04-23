import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Home from './Pages/Home';
import Login from './Pages/Login';
import Head from './Components/Head';
import Panier from './Pages/Panier';
import Register from './Pages/Register';
import AddArticle from './Pages/AddArticle';

/**
 * Renders the main application component.
 *
 * @return {JSX.Element} The rendered application component.
 */
function App(): JSX.Element {
  return (

    <BrowserRouter>
      <Head />

      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/login" element={<Login />} />
        <Route path="/panier" element={<Panier />} />
        <Route path="/register" element={<Register />} />
        <Route path="/addArticle" element={<AddArticle />} />


      </Routes>

    </BrowserRouter>
  );
}

export default App;
