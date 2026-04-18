import { Navigate } from "react-router-dom";
import { useAuth } from "./AuthProvider";


type ProtectedRouteProps = {
    children: JSX.Element
}
/**
 * A higher-order component that renders the protected route if the user is authenticated,
 * otherwise it redirects to the login page.
 *
 * @param {object} children - The child components to be rendered within the protected route.
 * @return {JSX.Element} The rendered child components within the protected route.
 *
 * @author [Gabriel LOPEZ](https://github.com/glopez-dev)
 */
export default function ProtectedRoute({ children }: ProtectedRouteProps): JSX.Element {
    const { getToken } = useAuth();
    const token = getToken();
    if (token === null) {
        return <Navigate to="/login" />;
    }
    return children;
}