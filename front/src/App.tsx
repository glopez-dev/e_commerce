import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Home from './Pages/Home';
import Login from './Pages/Login';
import Head from './Components/Head';

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


      </Routes>

    </BrowserRouter>
  );
}

export default App;
