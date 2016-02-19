package codereview

import (
	"testing"
	"net/url"
	"encoding/json"
	"github.com/stretchr/testify/assert"
	"time"
)

func TestPosts_create(t *testing.T) {
	setUp()
	c := NewClient()
	registerUser(c)
	imageId := imageId()
	addImage(c, imageId)
	res := addPost(c, imageId)
	assert.NotNil(t, res)
	assert.NotNil(t, res, res["postId"])
}

func TestPosts_getOne(t *testing.T) {
	setUp()
	c := NewClient()
	postId := addImageAndPost(c)
	post := c.getData("posts/" + postId, url.Values{})
	assert.NotNil(t, post)
	assert.NotNil(t, post["author"])
	assert.NotNil(t, post["postId"])
	assert.NotNil(t, post["created"])
	assert.NotNil(t, post["title"])
	assert.NotNil(t, post["topic"])
	assert.NotNil(t, post["score"])

	author := post["author"].(map[string]interface{})
	assert.NotNil(t, author["userId"])
	assert.NotNil(t, author["username"])

	assert.NotNil(t, post["points"])
	images := post["images"].([]interface{})
	assert.NotNil(t, images)
	assert.True(t, len(images) > 0)
}

func addImageAndPost(c *Client) string {
	registerUser(c)
	imageId := imageId()
	addImage(c, imageId)
	res := addPost(c, imageId)
	postId := floatToStr(res["postId"])
	return postId
}

func addPost(c *Client, imageId string) map[string]interface{} {
	arrayData, _ := json.Marshal([]string{imageId})
	imageIds := string(arrayData)
	return c.postData("posts", url.Values{"imageIds":{imageIds}, "title":{"Weird Person"}, "topic":{"搞笑"}});
}

func addImageAndPost2(c *Client, imageId string) string {
	addImage(c, imageId)
	post := addPost(c, imageId)
	postId := floatToStr(post["postId"])
	return postId
}

func imageId() string {
	return "2ypzvXw"
}

func imageId2() string {
	return "EZZRag"
}

func TestPost_list(t *testing.T) {
	setUp()
	c := NewClient()
	registerUser(c)
	imageId := imageId()
	addImage(c, imageId);
	addPost(c, imageId);
	time.Sleep(time.Second)
	addPost(c, imageId)
	posts := c.getArrayData("posts", url.Values{"limit":{"2"}})
	if len(posts) > 0 {
		post := posts[0].(map[string]interface{})
		assert.NotNil(t, post["commentCount"])
		assert.NotNil(t, post["author"])
		author := post["author"].(map[string]interface{})
		assert.NotNil(t, author)

		_, ok := post["vote"]
		assert.True(t, ok)

		cover := post["cover"].(map[string]interface{})
		assert.NotNil(t, cover["imageId"])
		assert.NotNil(t, cover["width"])
		assert.NotNil(t, cover["height"])
		assert.NotNil(t, cover["link"])
	}
}

func TestPost_vote(t *testing.T) {
	setUp()
	c := NewClient()
	postId := addImageAndPost(c)
	post := c.getData("posts/" + postId + "/vote/up", url.Values{})
	assert.NotNil(t, post)
	assert.Nil(t, post["vote"])

	post = c.getData("posts/" + postId, url.Values{})
	assert.Equal(t, toInt(post["ups"]), 1)
	assert.Equal(t, toInt(post["downs"]), 0)
	assert.Equal(t, toInt(post["points"]), 1)
	assert.Equal(t, "up", post["vote"])

	c.getData("posts/" + postId + "/vote/up", url.Values{})
	post = c.getData("posts/" + postId, url.Values{})
	assert.Equal(t, toInt(post["ups"]), 0)
	assert.Equal(t, toInt(post["downs"]), 0)
	assert.Nil(t, post["vote"])

	c.getData("posts/" + postId + "/vote/down", url.Values{})
	post = c.getData("posts/" + postId, url.Values{})
	assert.Equal(t, toInt(post["ups"]), 0)
	assert.Equal(t, toInt(post["downs"]), 1)
	assert.Equal(t, "down", post["vote"])

	c.getData("posts/" + postId + "/vote/up", url.Values{})
	post = c.getData("posts/" + postId, url.Values{})
	assert.Equal(t, toInt(post["ups"]), 1)
	assert.Equal(t, toInt(post["downs"]), 0)
}

func getPostId(post interface{}) string {
	return floatToStr(post.(map[string]interface{})["postId"])
}

func TestPost_sort(t *testing.T) {
	setUp()
	c := NewClient()
	registerUser(c)
	post1Id := addImageAndPost2(c, imageId())
	c.getData("posts/" + post1Id + "/vote/up", url.Values{})
	time.Sleep(time.Second)
	post2Id := addImageAndPost2(c, imageId2())

	registerUser2(c)
	c.getData("posts/" + post1Id + "/vote/up", url.Values{})

	posts := c.getArrayData("posts", url.Values{"sort":{"created"}})
	assert.Equal(t, getPostId(posts[0]), post2Id)
	assert.Equal(t, getPostId(posts[1]), post1Id)

	posts = c.getArrayData("posts", url.Values{"sort":{"score"}})
	assert.Equal(t, getPostId(posts[0]), post1Id)
	assert.Equal(t, getPostId(posts[1]), post2Id)
}
