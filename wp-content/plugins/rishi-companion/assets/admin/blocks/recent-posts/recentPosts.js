import { __ } from "@wordpress/i18n";
import apiFetch from "@wordpress/api-fetch";
import { addQueryArgs } from "@wordpress/url";
import { useEffect } from "@wordpress/element";

const DEFAULT_QUERY = {
    per_page: -1,
    orderby : "date",
    order   : "desc",
};

const RecentPosts = ({attributes, setAttributes}) => {

    const {
		recentPostLabel,
		recentPostCount,
		recentPostShowThumbnail,
		recentPostShowDate,
        layoutStyle,
		openInNewTab,
        recentPosts
	} = attributes;

    useEffect(() => {
        getRecentPosts();
    }, []);

	const getRecentPosts = () => {
        apiFetch({
            path: addQueryArgs(`/wp/v2/posts?_embed`, DEFAULT_QUERY),
            method: "GET",

        }).then((response) => {
            let posts = response?.map((item) => {
                let options = { year: 'numeric', month: 'long', day: 'numeric' };
                let date_new = new Date(item.date).toLocaleDateString("en-US", options);
                return {
                    id            : item.id,
                    image         : item?._embedded?.['wp:featuredmedia'] ? item._embedded?.['wp:featuredmedia']?.['0']?.source_url : "",
                    title         : item.title.rendered,
                    content       : item.content.rendered,
                    author        : item.author,
                    type          : item.type,
                    link          : item.link,
                    date          : item.date,
                    date_new      : date_new,
                    status        : item.status,
                    cat_lists     : item?._embedded?.['wp:term']['0'] ? item?._embedded?.['wp:term']['0'] : "",
                }
            });
            setAttributes( { recentPosts:posts } );

        }).catch((error) => {
            console.log(error);

        });
    };

    var target = openInNewTab ? '_blank' : '_self'
    
    return (
        <section id="rishi_recent_posts" className="rishi_sidebar_widget_recent_post">
            {recentPostLabel ? <h2 className="widget-title"><span>{recentPostLabel}</span></h2> : '' }
            <ul className={layoutStyle}>
                {recentPosts.slice(0,recentPostCount)?.map((item, index) => 
                    <li key={index}>
                        { recentPostShowThumbnail &&
                            <a target={target} href={item.link} className={`post-thumbnail ${item?.image ? '' : 'fallback-img'}`} rel="noopener"> 
                                { item?.image &&
                                    <img
                                        className="image-preview"
                                        src={item.image}
                                        alt="image-alt"
                                    />
                                }
                            </a>
                        }
                        <div className="widget-entry-header">
                            <>
                            {item.cat_lists &&
                                <span className="cat-links">
                                    { item.cat_lists?.map((cat_item,cat_index) =>
                                        <a key={cat_index} target={target} href={cat_item.link} rel="noopener">{cat_item.name}</a>
                                    )}
                                </span>
                            }   
                            </>                     
                            <h3 className="entry-title"><a target={target} href={item.link} rel="noopener">{item.title}</a></h3>
                            <>
                            { recentPostShowDate && item?.date &&
                                <div className="entry-meta">
                                    <span className="posted-on">
                                        <a target={target} href={item.link} rel="noopener">
                                            <time dateTime={item.date}>{item.date_new}</time>
                                        </a>
                                    </span>
                                </div>
                            }
                            </>
                        </div>
                    </li>
                )}
            </ul>
        </section>  
    );
    
}

export default RecentPosts;