package ru.devprom.tests;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Blogpost;
import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.blogs.BlogPage;
import ru.devprom.pages.project.blogs.BlogpostNewPage;


public class BlogTest extends ProjectTestBase {

	private String user = "WebTestUser";

	/**The method creates new Blog Post and then verify it.
	 * Additionally it logs all the existed blogposts data*/
	@Test(enabled=false)
	public void testCreateBlogPost() {
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(
						this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
	 
		Blogpost testB = new Blogpost("Test Post "+DataProviders.getUniqueString(), "Test blog content");
		testB.setAuthor(user);
		
		BlogPage bp = favspage.gotoBlog();
		BlogpostNewPage nbp = bp.addBlogpost();
		bp = nbp.createNewPost(testB);
		FILELOG.info("Blogpost created: "+testB.getId()+" : " + testB.getName());
		//
		Blogpost[] bs = bp.readAllPosts();
		for (Blogpost b:bs){
			FILELOG.debug(b);
		}
		//
		Blogpost havePost = bp.readPost(testB.getId());
		Assert.assertEquals(havePost.getName(), testB.getName());
		Assert.assertEquals(havePost.getContent(), testB.getContent());
		Assert.assertEquals(havePost.getAuthor(), testB.getAuthor());
	
	}
	
	
}
