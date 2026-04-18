import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Home from './Pages/Home';
import Login from './Pages/Login';
import Head from './Components/Head';
import Panier from './Pages/Panier';
import Register from './Pages/Register';
import AddArticle from './Pages/AddArticle';
import { AuthProvider } from "./Components/Authentication/AuthProvider";
import ProtectedRoute from "./Components/Authentication/ProtectedRoute";
import MyOrders from "./Pages/Order/MyOrders";
import Profil from "./Pages/User/Profil";
import MyProduct from "./Pages/User/MyProduct";
import Detail from './Pages/Detail';
import SuccessOrder from "./Pages/Order/SuccessOrder";

/**
 * Renders the main application component.
 *
 * @return {JSX.Element} The rendered application component.
 */
function App(): JSX.Element {
    return (

        <BrowserRouter>

            <AuthProvider>
                <>
                    <Head />
                    <Routes>

                        <Route path="/" element={<Home />} />
                        <Route path="/login" element={<Login />} />
                        <Route path="/register" element={<Register />} />
                        <Route path="/panier" element={
                            <ProtectedRoute>
                                <Panier />

                            </ProtectedRoute>
                        } />
                        <Route path="/user/orders" element={
                            <ProtectedRoute>
                                <MyOrders />
                            </ProtectedRoute>
                        } />
                        <Route path="/detail/:id" element={<Detail />} />
                        <Route path="/addArticle" element={
                            <ProtectedRoute>
                                <AddArticle />
                            </ProtectedRoute>
                        } />
                        <Route path="/user/products" element={
                            <ProtectedRoute>
                                <MyProduct />
                            </ProtectedRoute>
                        } />
                        <Route path="/user/profil" element={
                            <ProtectedRoute>
                                <Profil />
                            </ProtectedRoute>
                        } />
                        <Route path="/order/success" element={
                            <ProtectedRoute>
                                <SuccessOrder />
                            </ProtectedRoute>
                        } />
                    </Routes>
                </>
            </AuthProvider>
        </BrowserRouter>
    );
}

export default App;
