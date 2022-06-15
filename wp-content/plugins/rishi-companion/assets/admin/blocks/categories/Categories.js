import { __ } from "@wordpress/i18n";
import apiFetch from "@wordpress/api-fetch";
import { addQueryArgs } from "@wordpress/url";
import { useEffect } from "@wordpress/element";
import classnames from 'classnames';
import { __experimentalUseColorProps as useColorProps } from '@wordpress/block-editor'; //WordPress dependencies

const DEFAULT_QUERY = {
    per_page: -1,
};
const Categories = ({ attributes, setAttributes }) => {

    const {
        categoriesLabel,
        layoutStyle,
        category_selected,
        category,
        showPostCount
    } = attributes;

    useEffect(() => {
        getCategoriesList();
    }, [null]);

    useEffect(() => {
        getCategory();
    }, [category_selected]);

    //Find a way to fetch all categories through REST API.
    const getCategoriesList = () => {
        apiFetch({
            path: addQueryArgs(`wp/v2/categories`, DEFAULT_QUERY ),
            method: "GET",
        }).then((response) => {
            let categories = response?.map((item) => {
                return {
                    id: item.id,
                    count: item.count,
                    name: item.name,
                    taxonomy: item.taxonomy,
                    image: item.image_url,
                    link: item.link,
                }
            });
            setAttributes({ categoriesList: categories });

        }).catch((error) => {
            console.log(error);
        });
    };

    var cat = [];
    if( category_selected != null ){ 
        category_selected.forEach(element => {
            cat.push(element.value)
        });
        cat.join();
    }
    
    const getCategory = () => {
        apiFetch({
            path: addQueryArgs(`wp/v2/categories?include=${cat}`, DEFAULT_QUERY),
            method: "GET",
        }).then((response) => {
            let cat = response;
            setAttributes({ category: cat });
        }).catch((error) => {
            console.log(error);
        })
    }

    const colorProps = useColorProps( attributes );

    const categoryClass = classnames(
		'category-post-count',
		colorProps.className,
	);

    const categoryStyle = {
		...colorProps.style
	};

    return (
        <section id="rishi_categories" className="rishi_sidebar_widget_categories">
            {
                categoriesLabel &&
                <h2 className="widget-title" itemProp="name">
                    <span>{categoriesLabel}</span>
                </h2>
            }
            <ul id="rishi-categories-wrapper" className={layoutStyle.value}>
                {
                0 < category.length && category.map((item, index) => {
                        const imgUrl      = ( item['image_url'] ) ? item['image_url'] : '';
                        const imgClass    = ( item['image_url'] ) == '' ? 'fallback-img' : '';
                        const postCount   = ( item['count'] ) == 1 ? __(' Post', 'rishi-companion')  : __( ' Posts', 'rishi-companion');
                        return (
                            <li key={index}>
                                <a href={item['link']} className={imgClass} style={imgUrl && { backgroundImage: `url(${imgUrl})` } || {}}>
                                    { layoutStyle.value === 'layout-type-1' &&
                                        <span className="category-name">{item['name']}</span>
                                    }
                                    { layoutStyle.value === 'layout-type-1' && 
                                        showPostCount && (item['count'] != 0) &&
                                        <span className={`category-post-count rishi_sidebar_widget_categories ul li ${categoryClass}`} style={categoryStyle}>
                                            {item['count']}
                                            {postCount}
                                        </span>
                                    }    
                                    { layoutStyle.value === 'layout-type-2' &&
                                        <div className="category-content">
                                            <span className="category-name">{item['name']}</span>
                                            {
                                                showPostCount && (item['count'] != 0) &&
                                                <span className={`category-post-count rishi_sidebar_widget_categories ul li ${categoryClass}`} style={categoryStyle}>
                                                    {item['count']}
                                                    {postCount}
                                                </span>
                                            }
                                        </div>
                                    }
                                </a>
                            </li>
                        )
                    }
                    )
                }
            </ul>
        </section>
    );

}

export default Categories;