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

func TestComments_vote(t *testing.T) {
	setUp()
	c := NewClient()
	postId := addImageAndPost(c)
	commentId := addComment(c, postId)
	res := c.getData("posts/" + postId + "/comments/" + commentId + "/vote/up", url.Values{})
	assert.NotNil(t, res)
}