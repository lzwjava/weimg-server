package codereview

import (
	"testing"
	"net/url"
	"github.com/stretchr/testify/assert"
)

func TestComments_create(t *testing.T) {
	setUp()
	c := NewClient()
	postId := addImageAndPost(c)
	res := c.postData("posts/" + postId + "/comments", url.Values{"content": {"大惊小怪"}})
	assert.NotNil(t, res)
	assert.NotNil(t, res["commentId"])

	commentId := floatToStr(res["commentId"])
	res = c.postData("posts/" + postId + "/comments",
		url.Values{"content": {"呵呵"}, "parentId":{commentId}})
	assert.NotNil(t, res)
	assert.NotNil(t, res["commentId"])
}

func addComment(c *Client, postId string) string {
	res := c.postData("posts/" + postId + "/comments", url.Values{"content": {"大惊小怪"}})
	commentId := floatToStr(res["commentId"])
	return commentId
}

func TestComments_count(t *testing.T) {
	setUp()
	c := NewClient()
	postId := addImageAndPost(c)
	addComment(c, postId)
	post := c.getData("posts/" + postId, url.Values{})
	assert.True(t, toInt(post["commentCount"]) > 0)
}

func addPostAndComment(c *Client) (string, string) {
	postId := addImageAndPost(c)
	commentId := addComment(c, postId)
	return postId, commentId
}

func TestComments_vote(t *testing.T) {
	setUp()
	c := NewClient()
	postId, commentId := addPostAndComment(c)
	res := c.getData("posts/" + postId + "/comments/" + commentId + "/vote/up", url.Values{})
	assert.NotNil(t, res)
}

func TestComments_list(t *testing.T) {
	setUp()
	c := NewClient()
	postId, _ := addPostAndComment(c)
	res := c.getArrayData("posts/" + postId + "/comments", url.Values{})
	assert.NotNil(t, res)
	assert.Equal(t, len(res), 1)
	comment := res[0].(map[string]interface{})
	assert.NotNil(t, comment["author"])
	assert.NotNil(t, comment["commentId"])
	assert.NotNil(t, comment["postId"])
}
