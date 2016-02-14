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
	user := registerUser(c)
	imageId := imageId()
	addImage(c, imageId)
	res := addPost(c, imageId)
	assert.NotNil(t, res)
	assert.NotNil(t, res, res["postId"])

	postId := floatToStr(res["postId"])
	post := c.getData("posts/" + postId, url.Values{})
	assert.NotNil(t, post)
	assert.Equal(t, "Weird Person", post["title"])
	assert.Equal(t, user["userId"], post["author"])
	assert.Equal(t, "搞笑", post["topic"]);
	assert.NotNil(t, post["created"])
	assert.NotNil(t, post["postId"])
}

func TestPosts_getOne(t *testing.T) {
	setUp()
	c := NewClient()
	postId := addImageAndPost(c)
	post := c.getData("posts/" + postId, url.Values{})
	assert.NotNil(t, post)
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

func imageId() string {
	return "2ypzvXw"
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
	res := c.getData("posts/" + postId + "/vote/up", url.Values{})
	assert.NotNil(t, res)

	post := c.getData("posts/" + postId, url.Values{})
	assert.Equal(t, toInt(post["ups"]), 1)
	assert.Equal(t, toInt(post["downs"]), 0)
	assert.Equal(t, toInt(post["points"]), 1)

	c.getData("posts/" + postId + "/vote/up", url.Values{})
	post = c.getData("posts/" + postId, url.Values{})
	assert.Equal(t, toInt(post["ups"]), 0)
	assert.Equal(t, toInt(post["downs"]), 0)

	c.getData("posts/" + postId + "/vote/down", url.Values{})
	post = c.getData("posts/" + postId, url.Values{})
	assert.Equal(t, toInt(post["ups"]), 0)
	assert.Equal(t, toInt(post["downs"]), 1)

	c.getData("posts/" + postId + "/vote/up", url.Values{})
	post = c.getData("posts/" + postId, url.Values{})
	assert.Equal(t, toInt(post["ups"]), 1)
	assert.Equal(t, toInt(post["downs"]), 0)
}
