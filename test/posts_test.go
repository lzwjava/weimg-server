package codereview

import (
	"testing"
	"net/url"
	"encoding/json"
	"github.com/stretchr/testify/assert"
)

func TestPosts_create(t *testing.T) {
	c := NewClient()
	registerUser(c)
	imageId := "abcdef";
	addImage(c, imageId)
	array := make([]string, 1)
	array[0] = imageId
	arrayData, _ := json.Marshal(array)
	imageIds := string(arrayData)
	res := c.postData("posts", url.Values{"imageIds":{imageIds}, "title":{"Weird Person"}});
	assert.NotNil(t, res)
	assert.NotNil(t, res, res["postId"])

	postId := floatToStr(res["postId"])
	post := c.getData("posts/" + postId, url.Values{})
	assert.NotNil(t, post)
	assert.Equal(t, post["title"], "Weird Person");
}
