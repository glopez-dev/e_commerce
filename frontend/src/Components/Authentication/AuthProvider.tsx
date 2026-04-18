import React from "react";
import { useNavigate } from "react-router-dom";
import { jwtDecode } from "jwt-decode";

const AuthContext = React.createContext<
    {
        onLogin: (token: string) => void,
        onLogout: () => void,
        getToken: () => string | null,
        getDecodedToken: () => decodedToken | null,
        setToken: (token: string) => void,
    }>({
        onLogin: () => null,
        onLogout: () => null,
        getToken: () => null,
        getDecodedToken: () => null,
        setToken: () => null,
    });


/**
 *  Custom hook to access the AuthContext.
 * @returns {Object} The AuthContext object.
 */
const useAuth = () => React.useContext(AuthContext);

type AuthProviderProps = {
    children: JSX.Element
}

export type decodedToken = {
    exp: number,
    username: string,
    id: number,
    email: string,
};
/**
 * AuthProvider component for handling authentication.
 *
 * @param {Object} children - The child components to be wrapped by the AuthProvider.
 * @return {JSX.Element} The wrapped child components with the AuthContext Provider.
 */
function AuthProvider({ children }: AuthProviderProps) {
    const navigate = useNavigate();

    const setToken = (token: string) => {
        sessionStorage.setItem('token', token);
    }

    const handleLogin = (token: string) => {
        setToken(token);
        navigate("/");
    };

    const handleLogout = () => {
        sessionStorage.removeItem('token');
    };


    const getToken = () => {
        const token = sessionStorage.getItem('token');
        if (token === null) {
            return null;
        }
        return token;
    }

    const getDecodedToken = () => {
        let token = sessionStorage.getItem('token');
        if (token !== null) {
            return jwtDecode<decodedToken>(token);
        }
        return token;
    }

    const value = {
        onLogin: handleLogin,
        onLogout: handleLogout,
        getToken,
        getDecodedToken,
        setToken,
    };

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
}

export { AuthContext, AuthProvider, useAuth };