
import Article from '../Components/Article';
import Style from '../Styles/Home.module.css';



/**
 * @return
 * @constructor
 */
export default function Home(): JSX.Element {

    return (

        <>
            <div className={Style.containers}>
                <Article />

            </div>
        </>
    );
}