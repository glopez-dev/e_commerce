import {BrowserRouter, Routes, Route} from 'react-router-dom';
import Home from './Pages/Home';
import Login from './Pages/Login';
import Head from './Components/Head';
import Panier from './Pages/Panier';
import Register from './Pages/Register';
import AddArticle from './Pages/AddArticle';
import {AuthProvider} from "./Components/Authentication/AuthProvider";
import ProtectedRoute from "./Components/Authentication/ProtectedRoute";

/**
 * Renders the main application component.
 *
 * @return {JSX.Element} The rendered application component.
 */
function App(): JSX.Element {
    return (

        <BrowserRouter>
            <Head/>
            <AuthProvider>
                <Routes>

                    <Route path="/" element={<Home/>}/>
                    <Route path="/login" element={<Login/>}/>
                    <Route path="/register" element={<Register/>}/>
                    <Route path="/panier" element={
                        <ProtectedRoute>
                            <Panier/>
                        </ProtectedRoute>
                    }/>
                    <Route path="/addArticle" element={
                        <ProtectedRoute>
                            <AddArticle/>
                        </ProtectedRoute>
                    }/>
                </Routes>
            </AuthProvider>
        </BrowserRouter>
    );
}

export default App;
